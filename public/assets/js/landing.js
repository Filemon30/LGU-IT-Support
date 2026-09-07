document.addEventListener('DOMContentLoaded', function () {

function toggleHamburgerMenu() {
    const dropdown = document.getElementById('hamburgerDropdown');
    const btn = document.querySelector('.hamburger-btn');
    dropdown.classList.toggle('open');
    btn.classList.toggle('active');
}

document.addEventListener('click', function (e) {
    const dropdown = document.getElementById('hamburgerDropdown');
    const btn = document.querySelector('.hamburger-btn');

    if (dropdown && btn && !dropdown.contains(e.target) && !btn.contains(e.target)) {
        dropdown.classList.remove('open');
        btn.classList.remove('active');
    }
});

const hamburgerDropdown = document.getElementById('hamburgerDropdown');
if (hamburgerDropdown) {
    hamburgerDropdown.querySelectorAll('.nav-link').forEach(function (link) {
        link.addEventListener('click', function () {
            hamburgerDropdown.classList.remove('open');
            document.querySelector('.hamburger-btn').classList.remove('active');
        });
    });
}

const navLinks = document.querySelectorAll('.nav-link');
const sections = document.querySelectorAll('section[id]');
const headerButtons = document.querySelector('.header-buttons');

let lastSection = null;

/*
|--------------------------------------------------------------------------
| Smooth Scroll
|--------------------------------------------------------------------------
*/

navLinks.forEach(link => {
    link.addEventListener('click', function (e) {
        const href = this.getAttribute('href');

        if (!href.startsWith('#')) return;

        e.preventDefault();

        const targetId = href.substring(1);
        const targetSection = document.getElementById(targetId);

        if (!targetSection) return;

        const headerOffset = 80;

        const targetPosition =
            targetSection.getBoundingClientRect().top +
            window.scrollY -
            headerOffset;

        window.scrollTo({
            top: targetPosition,
            behavior: 'smooth'
        });
    });
});


/*
|--------------------------------------------------------------------------
| Active Navigation
|--------------------------------------------------------------------------
*/

function updateActiveNavLink() {

    const scrollPosition = window.scrollY + 120;

    let currentSection = null;

    /*
    |--------------------------------------------------------------------------
    | Detect Current Section
    |--------------------------------------------------------------------------
    */

    sections.forEach(section => {

        const sectionTop = section.offsetTop;
        const sectionBottom = sectionTop + section.offsetHeight;

        if (
            scrollPosition >= sectionTop &&
            scrollPosition < sectionBottom
        ) {
            currentSection = section;
        }

    });


    /*
    |--------------------------------------------------------------------------
    | Keep Last Section While Crossing Dividers
    |--------------------------------------------------------------------------
    |
    | Dividers live between sections, so the scroll position can fall
    | inside none of them. Reuse the last known section so the header
    | buttons don't flicker off while scrolling past a divider.
    |
    */

    if (!currentSection) {
        currentSection = lastSection;
    }


    /*
    |--------------------------------------------------------------------------
    | Detect Track Ticket at Bottom
    |--------------------------------------------------------------------------
    |
    | This makes sure Track Ticket becomes active when the user
    | reaches the bottom of the page.
    |
    */

    const scrollBottom =
        window.scrollY + window.innerHeight;

    const documentBottom =
        document.documentElement.scrollHeight;

    if (scrollBottom >= documentBottom - 10) {

        currentSection =
            document.getElementById('track-ticket');

    }

    lastSection = currentSection;


    /*
    |--------------------------------------------------------------------------
    | Update Active Navigation
    |--------------------------------------------------------------------------
    */

    navLinks.forEach(link => {

        link.classList.remove('active');

        if (
            currentSection &&
            link.getAttribute('href') === `#${currentSection.id}`
        ) {
            link.classList.add('active');
        }

    });


    /*
    |--------------------------------------------------------------------------
    | Show / Hide Header Buttons
    |--------------------------------------------------------------------------
    |
    | Home     = hidden
    | Others   = visible
    |
    */

    if (headerButtons) {

        if (
            currentSection &&
            currentSection.id !== 'home' &&
            window.innerWidth > 768
        ) {

            headerButtons.style.display = 'flex';

        } else {

            headerButtons.style.display = 'none';

        }

    }

}


/*
|--------------------------------------------------------------------------
| Scroll Event
|--------------------------------------------------------------------------
*/

window.addEventListener(
    'scroll',
    updateActiveNavLink,
    { passive: true }
);


/*
|--------------------------------------------------------------------------
| Initial State
|--------------------------------------------------------------------------
*/

updateActiveNavLink();

});