// assets/js/main.js

// Theme Toggle
const themeToggle = document.getElementById('themeToggle');
if (themeToggle) {
    themeToggle.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const icon = themeToggle.querySelector('i');
        if (document.body.classList.contains('dark-mode')) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            localStorage.setItem('theme', 'dark');
        } else {
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            localStorage.setItem('theme', 'light');
        }
    });
    
    // Load saved theme
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        themeToggle.querySelector('i').classList.remove('fa-moon');
        themeToggle.querySelector('i').classList.add('fa-sun');
    }
}

// Animated Counters
const counters = document.querySelectorAll('.stat-number');
const speed = 200;

const animateCounter = (counter) => {
    const target = parseInt(counter.getAttribute('data-count'));
    let count = 0;
    const increment = target / speed;
    
    const updateCount = () => {
        if (count < target) {
            count += increment;
            counter.innerText = Math.ceil(count);
            setTimeout(updateCount, 10);
        } else {
            counter.innerText = target;
        }
    };
    
    updateCount();
};

// Intersection Observer for counters
const observerOptions = {
    threshold: 0.5,
    rootMargin: '0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counter = entry.target;
            animateCounter(counter);
            observer.unobserve(counter);
        }
    });
}, observerOptions);

counters.forEach(counter => observer.observe(counter));

// Testimonial Swiper
const testimonialSwiper = new Swiper('.testimonial-swiper', {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    breakpoints: {
        768: {
            slidesPerView: 2,
        },
        992: {
            slidesPerView: 3,
        },
    },
});

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Navbar Scroll Effect
window.addEventListener('scroll', () => {
    const navbar = document.getElementById('mainNav');
    if (window.scrollY > 50) {
        navbar.style.background = 'rgba(255, 255, 255, 0.98)';
        navbar.style.boxShadow = '0 2px 20px rgba(0,0,0,0.1)';
    } else {
        navbar.style.background = 'rgba(255, 255, 255, 0.95)';
        navbar.style.boxShadow = 'none';
    }
});

// Quick Book Buttons
document.querySelectorAll('.quick-book').forEach(btn => {
    btn.addEventListener('click', () => {
        const carId = btn.dataset.id;
        window.location.href = `booking.php?vehicle_id=${carId}`;
    });
});

// Date Picker min dates
const dateInputs = document.querySelectorAll('input[type="date"]');
const today = new Date().toISOString().split('T')[0];
dateInputs.forEach(input => {
    if (!input.value) {
        input.min = today;
    }
});

// Form Validation
const forms = document.querySelectorAll('form');
forms.forEach(form => {
    form.addEventListener('submit', (e) => {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
        }
    });
});

// Loading Animation
window.addEventListener('load', () => {
    document.body.classList.add('loaded');
});

// Back to Top Button
const backToTop = document.createElement('button');
backToTop.innerHTML = '<i class="fas fa-arrow-up"></i>';
backToTop.className = 'back-to-top';
backToTop.style.cssText = `
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: none;
    cursor: pointer;
    display: none;
    z-index: 1000;
    transition: all 0.3s;
`;

document.body.appendChild(backToTop);

window.addEventListener('scroll', () => {
    if (window.scrollY > 300) {
        backToTop.style.display = 'block';
    } else {
        backToTop.style.display = 'none';
    }
});

backToTop.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

// Price Calculation on Booking Page
const pickupDate = document.querySelector('input[name="pickup_date"]');
const returnDate = document.querySelector('input[name="return_date"]');
const priceDisplay = document.querySelector('.total-price');

if (pickupDate && returnDate) {
    const calculateDays = () => {
        if (pickupDate.value && returnDate.value) {
            const pickup = new Date(pickupDate.value);
            const ret = new Date(returnDate.value);
            const days = Math.ceil((ret - pickup) / (1000 * 60 * 60 * 24));
            return days > 0 ? days : 1;
        }
        return 1;
    };
    
    const updatePrice = () => {
        const days = calculateDays();
        const dailyRate = parseFloat(document.querySelector('.daily-rate')?.dataset.rate || 0);
        const total = days * dailyRate;
        if (priceDisplay) {
            priceDisplay.textContent = `$${total.toFixed(2)}`;
        }
    };
    
    pickupDate.addEventListener('change', updatePrice);
    returnDate.addEventListener('change', updatePrice);
}