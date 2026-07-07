// ===== FORGOT PASSWORD JS START =====
document.addEventListener("DOMContentLoaded", () => {

    try {

        const forgotForm = document.getElementById("forgotPasswordForm");

        if (forgotForm) {

            forgotForm.addEventListener("submit", (e) => {

                e.preventDefault();

                const btn = forgotForm.querySelector("button[type='submit']");
                const success = document.getElementById("successMessage");

                btn.disabled = true;

                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Sending...';

                setTimeout(() => {

                    forgotForm.reset();

                    success.classList.remove("hidden");

                    btn.disabled = false;

                    btn.innerHTML = '<i class="fa-solid fa-paper-plane mr-2"></i>Send Reset Link';

                }, 1500);

            });

        }

    }
    catch (error) {

        console.error(error);

    }

});
// ===== FORGOT PASSWORD JS END =====
// ===== RESET PASSWORD JS START =====


const resetForm = document.getElementById("resetPasswordForm");

if (resetForm) {

    resetForm.addEventListener("submit", (e) => {

        e.preventDefault();

        const password = document.getElementById("newPassword").value;
        const confirm = document.getElementById("confirmPassword").value;

        if (password !== confirm) {

            alert("Passwords do not match.");

            return;

        }

        const btn = resetForm.querySelector("button[type='submit']");
        const success = document.getElementById("resetSuccess");

        btn.disabled = true;

        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Updating...';

        setTimeout(() => {

            resetForm.reset();

            success.classList.remove("hidden");

            btn.disabled = false;

            btn.innerHTML = '<i class="fa-solid fa-lock mr-2"></i>Reset Password';

        }, 1500);

    });

    document.querySelectorAll(".togglePassword").forEach(btn => {

        btn.addEventListener("click", () => {

            const input = document.getElementById(btn.dataset.target);

            const icon = btn.querySelector("i");

            if (input.type === "password") {

                input.type = "text";

                icon.classList.remove("fa-eye");

                icon.classList.add("fa-eye-slash");

            } else {

                input.type = "password";

                icon.classList.remove("fa-eye-slash");

                icon.classList.add("fa-eye");

            }

        });

    });

}
// ===== RESET PASSWORD JS END =====
