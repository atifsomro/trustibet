// ===== NOTIFICATION IN HEADER START =====
document.addEventListener("DOMContentLoaded", () => {
    try {
        const btn = document.getElementById("notificationBtn");
        const dropdown = document.getElementById("notificationDropdown");
        const notificationCount = document.getElementById("notificationCount");
        const markAllBtn = document.getElementById("markAllRead");
        btn?.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown.classList.toggle("hidden");
        });
        document.addEventListener("click", () => {
            dropdown?.classList.add("hidden");
        });
        dropdown?.addEventListener("click", (e) => {
            e.stopPropagation();
        });
        document.querySelectorAll(".notification-item").forEach(item => {
            item.addEventListener("click", () => {
                if (!item.classList.contains("unread")) return;
                item.classList.remove("unread");
                item.classList.remove("bg-green-500/5");
                const dot = item.querySelector(".notification-dot");
                if (dot) {
                    dot.remove();
                }
                let total = parseInt(notificationCount.textContent) || 0;
                if (total > 0) {
                    total--;
                    notificationCount.textContent = total;
                }
                if (total === 0) {
                    notificationCount.classList.add("hidden");
                }
            });
        });
        markAllBtn?.addEventListener("click", () => {
            document.querySelectorAll(".notification-item.unread").forEach(item => {
                item.classList.remove("unread");
                item.classList.remove("bg-green-500/5");
                const dot = item.querySelector(".notification-dot");
                if (dot) {
                    dot.remove();
                }
            });
            notificationCount.textContent = "0";
            notificationCount.classList.add("hidden");
        });
    }
    catch (error) {
        console.error(error);
    }
});
// ===== NOTIFICATION IN HEADER END =====
// ===== NOTIFICATION PAGE START =====
document.addEventListener("DOMContentLoaded", () => {
    try {
        const notificationList = document.getElementById("notificationList");
        const markAllBtn = document.getElementById("markAllReadPage");
        const deleteAllBtn = document.getElementById("deleteAllNotifications");
        function updateStats() {
            const total = document.querySelectorAll(".notification-card").length;
            const unread = document.querySelectorAll(".notification-card.unread").length;
            const read = total - unread;
            document.getElementById("totalNotifications").textContent = total;
            document.getElementById("unreadNotifications").textContent = unread;
            document.getElementById("readNotifications").textContent = read;
            const headerCount = document.getElementById("notificationCount");
            if (headerCount) {
                if (unread > 0) {
                    headerCount.classList.remove("hidden");
                    headerCount.textContent = unread;
                } else {
                    headerCount.classList.add("hidden");
                }
            }
            if (total === 0) {
                notificationList.innerHTML = `
                <div class="py-20 text-center">
                <i class="fa-regular fa-bell text-6xl text-brand-primary mb-6"></i>
                <h3>No Notifications Yet</h3>
                <p class="mt-3 opacity-70">
                You'll see all account activities here.
                </p>
                <a href="/" class="btn-primary inline-flex mt-8">
                Back To Dashboard
                </a>
                </div>
                `;
            }
        }
        document.addEventListener("click", (e) => {
            if (e.target.classList.contains("notificationRead")) {
                const card = e.target.closest(".notification-card");
                card.classList.remove("unread", "bg-green-500/5");
                const dot = card.querySelector("span");
                if (dot) {
                    dot.remove();
                }
                e.target.remove();
                updateStats();
            }
            if (e.target.classList.contains("notificationDelete")) {
                const card = e.target.closest(".notification-card");
                card.style.opacity = "0";
                card.style.transform = "translateX(80px)";
                setTimeout(() => {
                    card.remove();
                    updateStats();
                }, 300);
            }
        });
        markAllBtn?.addEventListener("click", () => {
            document.querySelectorAll(".notification-card.unread").forEach(card => {
                card.classList.remove("unread", "bg-green-500/5");
                const dot = card.querySelector("span");
                if (dot) {
                    dot.remove();
                }
                const btn = card.querySelector(".notificationRead");
                if (btn) {
                    btn.remove();
                }
            });
            updateStats();
        });
        deleteAllBtn?.addEventListener("click", () => {
            if (!confirm("Delete all notifications?")) return;
            document.querySelectorAll(".notification-card").forEach(card => {
                card.remove();
            });
            updateStats();
        });
        updateStats();
    }
    catch (error) {
        console.error(error);
    }
});
// ===== NOTIFICATION PAGE END =====
