import '../css/app.css';
import Swal from 'sweetalert2';
window.Swal = Swal;


const menuToggle = document.getElementById("MenuToggle");
const menuClose = document.getElementById("MenuClose");
const mobileMenu = document.getElementById("MobileMenu");
const overlay = document.getElementById("Overlay");

function openMenu() {
    mobileMenu.classList.remove("right-[-100%]");
    mobileMenu.classList.add("right-0");

    overlay.classList.remove("hidden");

    document.body.classList.add("overflow-hidden");
}

function closeMenu() {
    mobileMenu.classList.remove("right-0");
    mobileMenu.classList.add("right-[-100%]");

    overlay.classList.add("hidden");

    document.body.classList.remove("overflow-hidden");
}

menuToggle?.addEventListener("click", openMenu);
menuClose?.addEventListener("click", closeMenu);
overlay?.addEventListener("click", closeMenu);

document.addEventListener("keydown", (e) => {

    if (e.key === "Escape") {

        closeMenu();

    }

});

// ====== HEADER JS END ======
// ====== REGISTER FORM VALIDATION START ======
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registerForm");
    if (!form) return;
    const fname = document.getElementById("fname");
    const uname = document.getElementById("username");
    const email = document.getElementById("email");
    const phone = document.getElementById("phone");
    const country = document.getElementById("country_id");
    const fnameError = document.getElementById("fnameError");
    const unameError = document.getElementById("unameError");
    const emailError = document.getElementById("emailError");
    const phoneError = document.getElementById("phoneError");
    const countryError = document.getElementById("countryError");
    const password = document.getElementById("password");
    const passwordError = document.getElementById("passwordError");
    const passwordStrengthBar = document.getElementById("passwordStrengthBar");
    const passwordStrengthText = document.getElementById("passwordStrengthText");

    //==========================
    // Full Name
    //==========================
    function validateFullName() {
        let value = fname.value;

        // Sirf multiple consecutive spaces ko single space banao
        value = value.replace(/\s{2,}/g, " ");

        fname.value = value;

        // Validation ke liye trim use karo, input me value modify mat karo
        const trimmedValue = value.trim();

        if (trimmedValue === "") {
            fnameError.textContent = "Full name is required.";
            return false;
        }

        if (trimmedValue.length < 3) {
            fnameError.textContent = "Minimum 3 characters required.";
            return false;
        }

        if (trimmedValue.length > 30) {
            fnameError.textContent = "Maximum 30 characters allowed.";
            return false;
        }

        if (!/^[A-Za-z ]+$/.test(trimmedValue)) {
            fnameError.textContent = "Only letters and spaces are allowed.";
            return false;
        }

        fnameError.textContent = "";
        return true;
    }

    //==========================
    // Username
    //==========================
    function validateUsername() {
        const value = uname.value.trim();
        if (value === "") {
            unameError.textContent = "Username is required.";
            return false;
        }
        if (value.length > 20) {
            unameError.textContent = "Maximum 20 characters allowed.";
            return false;
        }
        if (!/^[A-Za-z].*$/.test(value)) {
            unameError.textContent = "Must start with a letter.";
            return false;
        }
        unameError.textContent = "";
        return true;
    }
    //==========================
    // Email
    //==========================
    function validateEmail() {
        const value = email.value.trim();
        const regex =
            /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/;
        if (value === "") {
            emailError.textContent = "Email address is required.";
            return false;
        }
        if (!regex.test(value)) {
            emailError.textContent = "Enter a valid email address.";
            return false;
        }
        emailError.textContent = "";
        return true;
    }
    //==========================
    // Country Validation
    //==========================
    function validateCountry() {
        if (country.value === "") {
            countryError.textContent = "Please select your country.";
            return false;
        }
        countryError.textContent = "";
        return true;
    }
    //==========================
    // Phone Validation
    //==========================
    function validatePhone() {
        const value = phone.value.trim();
        if (value === "") {
            phoneError.textContent = "Number is required.";
            return false;
        }
        const regex = /^\+?[0-9]{7,14}$/;
        if (!regex.test(value)) {
            phoneError.textContent =
                "Enter a valid number (7-14 digits).";
            return false;
        }
        phoneError.textContent = "";
        return true;
    }
    phone.addEventListener("input", function () {
        this.value = this.value
            .replace(/[^\d+]/g, "")
            .replace(/(?!^)\+/g, "")
            .slice(0, 14);
        validatePhone();
    });
    //==========================
    // Password Validation
    //==========================
    function validatePassword() {
        const value = password.value;
        if (value === "") {
            passwordError.textContent = "Password is required.";
            passwordStrengthBar.style.width = "0%";
            passwordStrengthText.textContent = "";
            return false;
        }
        if (value.length < 8) {
            passwordError.textContent = "Password must be at least 8 characters.";
            return false;
        }
        if (!/[A-Z]/.test(value)) {
            passwordError.textContent = "At least one uppercase letter is required.";
            return false;
        }
        if (!/[a-z]/.test(value)) {
            passwordError.textContent = "At least one lowercase letter is required.";
            return false;
        }
        if (!/[0-9]/.test(value)) {
            passwordError.textContent = "At least one number is required.";
            return false;
        }
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
            passwordError.textContent = "At least one special character is required.";
            return false;
        }
        passwordError.textContent = "";
        return true;
    }
    //==========================
    // Live Validation
    //==========================
    fname.addEventListener("input", validateFullName);
    uname.addEventListener("input", validateUsername);
    email.addEventListener("input", validateEmail);
    country.addEventListener("change", validateCountry);
    phone.addEventListener("input", validatePhone);
    password.addEventListener("input", updatePasswordStrength);
    //==========================
    // Submit
    //==========================
    form.addEventListener("submit", function (e) {
        const a = validateFullName();
        const b = validateUsername();
        const c = validateEmail();
        const d = validatePhone();
        const e1 = validateCountry();
        const f = validatePassword();
        if (!(a && b && c && d && e1 && f)) {
            e.preventDefault();
        }
    });
    //==========================
    // Password Strength
    //==========================
    function updatePasswordStrength() {
        const value = password.value;
        let score = 0;
        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[a-z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;
        if (score <= 2) {
            passwordStrengthBar.style.width = "33%";
            passwordStrengthBar.className = "h-full bg-red-500 transition-all duration-300";
            passwordStrengthText.textContent = "Weak Password";
            passwordStrengthText.className = "mt-2 block text-red-500";
        }
        else if (score <= 4) {
            passwordStrengthBar.style.width = "66%";
            passwordStrengthBar.className = "h-full bg-yellow-500 transition-all duration-300";
            passwordStrengthText.textContent = "Medium Password";
            passwordStrengthText.className = "mt-2 block text-yellow-500";
        }
        else {
            passwordStrengthBar.style.width = "100%";
            passwordStrengthBar.className = "h-full bg-green-500 transition-all duration-300";
            passwordStrengthText.textContent = "Strong Password";
            passwordStrengthText.className = "mt-2 block text-green-500";
        }
    }
});
// ====== REGISTER FORM VALIDATION END ======
// ====== LOGIN FORM VALIDATION START ======
(() => {
    document.addEventListener("DOMContentLoaded", () => {
        try {
            const form = document.getElementById("loginForm");
            if (!form) return;
            const email = document.getElementById("loginEmail");
            const password = document.getElementById("loginPassword");
            const emailError = document.getElementById("loginEmailError");
            const passwordError = document.getElementById("loginPasswordError");
            if (!email || !password || !emailError || !passwordError) return;
            function validateEmail() {
                const value = email.value.trim();
                const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[A-Za-z]{2,}$/;
                if (value === "") {
                    emailError.textContent = "Email address is required.";
                    return false;
                }
                if (!regex.test(value)) {
                    emailError.textContent = "Please enter a valid email address.";
                    return false;
                }
                emailError.textContent = "";
                return true;
            }
            function validatePassword() {
                const value = password.value;
                if (value === "") {
                    passwordError.textContent = "Password is required.";
                    return false;
                }
                if (value.length < 8) {
                    passwordError.textContent = "Password must be at least 8 characters.";
                    return false;
                }
                passwordError.textContent = "";
                return true;
            }
            email.addEventListener("input", validateEmail);
            password.addEventListener("input", validatePassword);
            form.addEventListener("submit", function (e) {
                const isEmailValid = validateEmail();
                const isPasswordValid = validatePassword();
                if (!isEmailValid || !isPasswordValid) {
                    e.preventDefault();
                }
            });
        }
        catch (error) {
            console.error("Login Validation Error:", error);
        }
    });
})();
document.addEventListener("DOMContentLoaded", () => {
    try {
        document.querySelectorAll(".toggle-password").forEach((button) => {
            button.addEventListener("click", function () {
                const wrapper = this.closest(".relative");
                if (!wrapper) return;

                const input = wrapper.querySelector(".password-field");
                const icon = this.querySelector(".password-icon");

                if (!input || !icon) return;

                const isPassword = input.type === "password";

                input.type = isPassword ? "text" : "password";

                icon.classList.toggle("fa-eye", !isPassword);
                icon.classList.toggle("fa-eye-slash", isPassword);
            });
        });
    } catch (error) {
        console.error("Password Toggle Error:", error);
    }
});
// ====== LOGIN FORM VALIDATION END ======
// ====== WELCOME BONUS MODAL START ======
document.addEventListener("DOMContentLoaded", () => {
    try {
        const modal = document.getElementById("welcomeModal");
        if (!modal) return;
        const closeBtn = document.getElementById("closeWelcomeModal");
        const claimBtn = document.getElementById("claimReward");
        function openModal() {
            modal.classList.remove("hidden");
            modal.classList.add("flex");
            document.body.classList.add("overflow-hidden");
        }
        function closeModal() {
            modal.classList.remove("flex");
            modal.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");
        }
        setTimeout(openModal, 600);
        closeBtn?.addEventListener("click", closeModal);
        claimBtn?.addEventListener("click", closeModal);
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape") {
                closeModal();
            }
        });
    } catch (error) {
        console.error("Welcome Reward Modal Error:", error);
    }
});
// ====== WELCOME BONUS MODAL END ======
// ===== DRAW COUNTDOWN START =====
document.addEventListener("DOMContentLoaded", () => {
    try {
        const days = document.getElementById("days");
        const hours = document.getElementById("hours");
        const minutes = document.getElementById("minutes");
        const seconds = document.getElementById("seconds");
        if (!days || !hours || !minutes || !seconds) return;
        // Change this date according to your draw
        const drawDate = new Date("2026-07-30T23:59:59").getTime();
        function updateCountdown() {
            const now = new Date().getTime();
            const distance = drawDate - now;
            if (distance <= 0) {
                days.textContent = "00";
                hours.textContent = "00";
                minutes.textContent = "00";
                seconds.textContent = "00";
                return;
            }
            days.textContent = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, "0");
            hours.textContent = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)))
                .padStart(2, "0");
            minutes.textContent = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(
                2, "0");
            seconds.textContent = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, "0");
        }
        updateCountdown();
        setInterval(updateCountdown, 1000);
    } catch (error) {
        console.error(error);
    }
});
// ====== DRAW COUNTDOWN END ======
// ===== DRAW JOIN PLAYER START =====
document.addEventListener("DOMContentLoaded", () => {
    try {
        const counter = document.getElementById("joinedPlayers");
        if (!counter) return;

        const target = 28451;
        let current = 0;
        const speed = Math.ceil(target / 100);

        function updateCounter() {
            current += speed;

            if (current >= target) {
                current = target;
            }

            counter.textContent = current.toLocaleString();

            if (current < target) {
                requestAnimationFrame(updateCounter);
            }
        }

        updateCounter();

    } catch (error) {
        console.error("Entry Counter:", error);
    }
});
// ===== DRAW JOIN PLAYER END =====
// ===== DRAW JOIN MESSAGE START =====
document.addEventListener("DOMContentLoaded", () => {
    try {
        const liveJoinMessage = document.getElementById("liveJoinMessage");
        if (!liveJoinMessage) return;

        const messages = [
            "🟢 3 players joined in the last minute",
            "🔥 7 new entries received",
            "🎟 Someone just joined the draw",
            "👥 12 players entered recently",
            "⚡ Hurry! Entries are filling fast",
            "✅ A new participant just joined"
        ];

        setInterval(() => {
            liveJoinMessage.classList.add("opacity-0");

            setTimeout(() => {
                const random = Math.floor(Math.random() * messages.length);
                liveJoinMessage.textContent = messages[random];
                liveJoinMessage.classList.remove("opacity-0");
            }, 300);

        }, 5000);

    } catch (error) {
        console.error("Live Messages:", error);
    }
});
// ===== DRAW JOIN MESSAGE END =====
// ===== WINNER MODAL POPUP JS START =====
document.addEventListener("DOMContentLoaded", () => {
    try {
        const modal = document.getElementById("winnerModal");
        const close = document.getElementById("closeWinnerModal");
        if (!modal || !close) return;
        document.querySelectorAll(".viewWinner").forEach(btn => {
            btn.addEventListener("click", () => {
                document.getElementById("winnerAvatar").src = btn.dataset.avatar;
                document.getElementById("winnerName").textContent = btn.dataset.name;
                document.getElementById("winnerPrize").textContent = btn.dataset.prize;
                document.getElementById("winnerNumber").textContent = btn.dataset.number;
                document.getElementById("winnerDraw").textContent = btn.dataset.draw;
                document.getElementById("winnerDate").textContent = btn.dataset.date;
                const status = document.getElementById("winnerStatus");
                status.textContent = btn.dataset.status;
                status.className = "inline-block mt-3 px-4 py-2 rounded-full text-sm";
                if (btn.dataset.status === "Paid") {
                    status.classList.add("bg-green-500/20", "text-green-500");
                } else {
                    status.classList.add("bg-yellow-500/20", "text-yellow-500");
                }
                modal.classList.remove("hidden");
                modal.classList.add("flex");
            });
        });
        close.addEventListener("click", () => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        });
        modal.addEventListener("click", (e) => {
            if (e.target === modal) {
                modal.classList.add("hidden");
                modal.classList.remove("flex");
            }
        });
    }
    catch (error) {
        console.error(error);
    }
});
// ===== WINNER MODAL POPUP JS END =====
// ===== HERO COUNTERS JS START =====
document.addEventListener("DOMContentLoaded", () => {
    const members = document.getElementById("activeMembers");
    const rewards = document.getElementById("rewardsPaid");
    if (members) {
        let value = parseInt(localStorage.getItem("activeMembers")) || 25000;
        members.textContent = (value / 1000).toFixed(1) + "K+";
        setInterval(() => {
            value += Math.floor(Math.random() * 80) + 20;
            members.textContent = (value / 1000).toFixed(1) + "K+";
            localStorage.setItem("activeMembers", value);
        }, 3000);
    }
    if (rewards) {
        let value = parseInt(localStorage.getItem("rewardsPaid")) || 2000000;
        rewards.textContent = "$" + (value / 1000000).toFixed(2) + "M+";
        setInterval(() => {
            value += Math.floor(Math.random() * 5000) + 1000;
            rewards.textContent = "$" + (value / 1000000).toFixed(2) + "M+";
            localStorage.setItem("rewardsPaid", value);
        }, 4000);
    }
});
// ===== HERO COUNTERS JS END =====
// ===== KYC Validation JS START =====

// ===== KYC Validation JS END =====
