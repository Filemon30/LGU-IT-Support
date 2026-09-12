/*
|--------------------------------------------------------------------------
| Ticket Queue — processes 1 modal at a time
|--------------------------------------------------------------------------
*/

var ticketQueue = [];
var ticketProcessing = false;

function processTicketQueue() {
    if (ticketProcessing || ticketQueue.length === 0) return;

    ticketProcessing = true;
    var data = ticketQueue.shift();
    var displayMessage = buildTicketMessage(data, false);
    var speechMessage = buildTicketMessage(data, true);

    document.getElementById('new-ticket-message').innerHTML = displayMessage;

    openModal('new-ticket-modal');

    playNotificationSound(function () {
        speak(speechMessage, function () {
            closeModal('new-ticket-modal');
            ticketProcessing = false;
            processTicketQueue();
        });
    });
}

function queueTicket(data) {
    ticketQueue.push(data);
    processTicketQueue();
    updateDashboardCounters(data);
}


/*
|--------------------------------------------------------------------------
| Dashboard Counter Updates
|--------------------------------------------------------------------------
*/

function incrementElement(id) {
    var el = document.getElementById(id);
    if (el) {
        el.textContent = parseInt(el.textContent || '0', 10) + 1;
    }
}

function decrementElement(id) {
    var el = document.getElementById(id);
    if (el) {
        var current = parseInt(el.textContent || '0', 10);
        if (current > 0) {
            el.textContent = current - 1;
        }
    }
}

function updateDashboardCounters(data) {
    incrementElement('total-tickets');
    incrementElement('pending-count');
    incrementElement('unassigned-count');

    var priority = (data.priority_name || '').toLowerCase();
    if (priority === 'critical') {
        incrementElement('priority-critical');
    } else if (priority === 'high') {
        incrementElement('priority-high');
    } else if (priority === 'medium') {
        incrementElement('priority-medium');
    } else if (priority === 'low') {
        incrementElement('priority-low');
    }

    var cat = (data.category_name || '').toLowerCase();
    var status = data.ticket_status || 'Pending';
    var prefix = '';
    if (cat === 'hardware') {
        prefix = 'hw';
    } else if (cat === 'software') {
        prefix = 'sw';
    } else if (cat === 'network') {
        prefix = 'net';
    }

    if (prefix) {
        incrementElement(prefix + '-total');
        if (status === 'Pending') {
            incrementElement(prefix + '-pending');
        } else if (status === 'On Progress') {
            incrementElement(prefix + '-progress');
        } else if (status === 'Resolved') {
            incrementElement(prefix + '-resolved');
        }
    }
}


/*
|--------------------------------------------------------------------------
| Modal
|--------------------------------------------------------------------------
*/

function openModal(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
}

function closeModal(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}


/*
|--------------------------------------------------------------------------
| Notification Sound
|--------------------------------------------------------------------------
*/

function playNotificationSound(callback) {
    try {
        var ctx = new (window.AudioContext || window.webkitAudioContext)();

        var notes = [523.25, 659.25, 783.99];
        var delay = 0;

        notes.forEach(function (freq) {
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.value = freq;

            gain.gain.setValueAtTime(0.3, ctx.currentTime + delay);
            gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + delay + 0.4);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(ctx.currentTime + delay);
            osc.stop(ctx.currentTime + delay + 0.4);

            delay += 0.15;
        });

        if (callback) {
            setTimeout(callback, 2000);
        }
    } catch (e) {
        if (callback) callback();
    }
}


/*
|--------------------------------------------------------------------------
| Text-to-Speech
|--------------------------------------------------------------------------
*/

function speak(text, onEnd) {
    if (!('speechSynthesis' in window)) {
        if (onEnd) onEnd();
        return;
    }
    window.speechSynthesis.cancel();
    var u = new SpeechSynthesisUtterance(text);
    u.rate = 0.85;
    u.pitch = 1.2;
    u.volume = 2;

    if (onEnd) {
        u.onend = onEnd;
    }

    var voices = window.speechSynthesis.getVoices();

    if (voices.length === 0) {
        window.speechSynthesis.onvoiceschanged = function () {
            voices = window.speechSynthesis.getVoices();
            setFemaleVoice(u, voices);
            window.speechSynthesis.speak(u);
        };
    } else {
        setFemaleVoice(u, voices);
        window.speechSynthesis.speak(u);
    }
}

function setFemaleVoice(utterance, voices) {
    var female = voices.find(function (v) {
        return v.name.toLowerCase().includes('female') ||
               v.name.toLowerCase().includes('samantha') ||
               v.name.toLowerCase().includes('zira') ||
               v.name.toLowerCase().includes('hazel') ||
               v.name.toLowerCase().includes('karen') ||
               v.name.toLowerCase().includes('moira') ||
               v.name.toLowerCase().includes('tessa') ||
               v.name.toLowerCase().includes('microsoft zira');
    });

    if (female) {
        utterance.voice = female;
    }
}


/*
|--------------------------------------------------------------------------
| Build Message
|--------------------------------------------------------------------------
*/

function buildTicketMessage(data, forSpeech) {
    var requester = data.requester_name || 'unknown';
    var type = data.requester_type || '';
    var category = data.category_name || '';
    var priority = data.priority_name || '';
    var label = forSpeech ? 'Baranggay' : 'Barangay';

    var sep1 = forSpeech ? ', ' : ' ';
    var sep2 = forSpeech ? ', ' : ' ';
    var msg = 'A new ticket submitted for' + sep1 + (category || 'unknown') + sep2 + 'from ';

    if (type === 'Office Division') {
        msg += requester + ' Division.';
    } else {
        msg += label + ' ' + requester + '.';
    }

    if (priority) {
        if (forSpeech) {
            msg += ' Priority, ' + priority + '.';
        } else {
            msg += '<br><p class="font-regular text-gray-600">Priority: ' + priority + '</p>';
        }
    }

    return msg;
}


/*
|--------------------------------------------------------------------------
| Reverb WebSocket Channel
|--------------------------------------------------------------------------
*/

function initTicketChannel() {
    console.log('Initializing ticket channel...');

    var pusherKey = document.querySelector('meta[name="reverb-key"]');
    var pusherHost = document.querySelector('meta[name="reverb-host"]');
    var pusherPort = document.querySelector('meta[name="reverb-port"]');

    console.log('Reverb elements:', {
        key: pusherKey,
        host: pusherHost,
        port: pusherPort
    });

    if (!pusherKey || !pusherHost) {
        console.error('Reverb meta tags not found');
        return;
    }

    var key = pusherKey.getAttribute('content');
    var host = pusherHost.getAttribute('content');
    var port = pusherPort ? pusherPort.getAttribute('content') : '8080';

    console.log('Connecting to Reverb:', {
        key: key,
        host: host,
        port: port
    });

    var pusher = new Pusher(key, {
        cluster: 'mt1',
        wsHost: host,
        wsPort: parseInt(port),
        wssPort: parseInt(port),
        forceTLS: false,
        enabledTransports: ['ws'],
        disableStats: true,
    });

    pusher.connection.bind('connected', function () {
        console.log('✅ Pusher connected to Reverb');

        var channel = pusher.subscribe('tickets');

        console.log('📡 Subscribing to tickets channel...');

        channel.bind('pusher:subscription_succeeded', function () {
            console.log('✅ Subscribed to tickets channel');
        });

        channel.bind('pusher:subscription_error', function (err) {
            console.error('❌ Subscription error:', err);
        });

        channel.bind('new-ticket', function (data) {
            console.log('🎫 NEW TICKET RECEIVED:', data);

            queueTicket(data);
        });

        channel.bind('ticket-assigned', function (data) {
            console.log('👤 TICKET ASSIGNED:', data);

            decrementElement('unassigned-count');
            incrementElement('confirmed-count');

            var pendingEl = document.getElementById('pending-count');
            if (pendingEl && parseInt(pendingEl.textContent || '0', 10) > 0) {
                pendingEl.textContent = parseInt(pendingEl.textContent || '0', 10) - 1;
            }
        });
    });

    pusher.connection.bind('error', function (err) {
        console.error('❌ Pusher connection error:', err);
    });

    pusher.connection.bind('disconnected', function () {
        console.warn('⚠️ Pusher disconnected');
    });

    pusher.connection.bind('failed', function () {
        console.error('❌ Pusher connection failed');
    });
}
