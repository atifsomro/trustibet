// ===== NOTIFICATION IN HEADER START =====
document.addEventListener("DOMContentLoaded", () => {
    try {
        const btn = document.getElementById("notificationBtn");
        const dropdown = document.getElementById("notificationDropdown");
        const notificationCount = document.getElementById("notificationCount");
        const markAllBtn = document.getElementById("markAllRead");

        btn?.addEventListener("click", (e) => {
            e.stopPropagation();
            dropdown?.classList.toggle("hidden");
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

                let total = parseInt(notificationCount?.textContent || "0", 10) || 0;
                if (total > 0) {
                    total--;
                    notificationCount.textContent = total;
                }

                if (total === 0) {
                    notificationCount?.classList.add("hidden");
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
            notificationCount?.classList.add("hidden");
        });
    } catch (error) {
        console.error(error);
    }
});
// ===== NOTIFICATION IN HEADER END =====
// ===== NOTIFICATION PAGE START =====
document.addEventListener("DOMContentLoaded", () => {
    try {
        const notificationList = document.getElementById("notificationList");
        const markAllBtn = document.getElementById("markAllReadPage");
        const pagination = document.getElementById("notificationPagination");
        const pageSize = 5;
        let currentPage = 1;

        function getCards() {
            return Array.from(document.querySelectorAll(".notification-card"));
        }

        function updateStats() {
            const cards = getCards();
            const total = cards.length;
            const unread = cards.filter(card => card.classList.contains("unread")).length;
            const read = total - unread;

            const totalEl = document.getElementById("totalNotifications");
            const unreadEl = document.getElementById("unreadNotifications");
            const readEl = document.getElementById("readNotifications");

            if (totalEl) totalEl.textContent = total;
            if (unreadEl) unreadEl.textContent = unread;
            if (readEl) readEl.textContent = read;

            const headerCount = document.getElementById("notificationCount");
            if (headerCount) {
                if (unread > 0) {
                    headerCount.classList.remove("hidden");
                    headerCount.textContent = unread;
                } else {
                    headerCount.classList.add("hidden");
                }
            }

            if (notificationList && total === 0) {
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
                if (pagination) {
                    pagination.innerHTML = "";
                }
                return;
            }

            showPage(currentPage);
        }

        function renderPagination(totalPages) {
            if (!pagination) return;

            if (totalPages <= 1) {
                pagination.innerHTML = "";
                return;
            }

            const buttons = [];
            buttons.push(`
                <button type="button" class="btn-secondary page-btn ${currentPage === 1 ? "opacity-50 pointer-events-none" : ""}" data-action="prev">
                    Previous
                </button>
            `);

            for (let page = 1; page <= totalPages; page++) {
                buttons.push(`
                    <button type="button" class="page-btn ${page === currentPage ? "btn-primary" : "btn-secondary"}" data-page="${page}">
                        ${page}
                    </button>
                `);
            }

            buttons.push(`
                <button type="button" class="btn-secondary page-btn ${currentPage === totalPages ? "opacity-50 pointer-events-none" : ""}" data-action="next">
                    Next
                </button>
            `);

            pagination.innerHTML = buttons.join("");
            pagination.querySelectorAll(".page-btn").forEach(btn => {
                btn.addEventListener("click", () => {
                    if (btn.dataset.action === "prev") {
                        if (currentPage > 1) {
                            showPage(currentPage - 1);
                        }
                        return;
                    }

                    if (btn.dataset.action === "next") {
                        if (currentPage < totalPages) {
                            showPage(currentPage + 1);
                        }
                        return;
                    }

                    const page = Number(btn.dataset.page);
                    if (page) {
                        showPage(page);
                    }
                });
            });
        }

        function showPage(page) {
            const cards = getCards();
            const totalPages = Math.max(1, Math.ceil(cards.length / pageSize));
            currentPage = Math.min(Math.max(page, 1), totalPages);

            cards.forEach((card, index) => {
                const start = (currentPage - 1) * pageSize;
                const end = start + pageSize;
                card.style.display = index >= start && index < end ? "" : "none";
            });

            renderPagination(totalPages);
        }

        function createReadLabel() {
            const labelWrapper = document.createElement("div");
            labelWrapper.className = "mt-5";
            labelWrapper.innerHTML = `<span class="text-sm text-green-500">Read</span>`;
            return labelWrapper;
        }

        function markCardRead(card) {
            card.classList.remove("unread", "bg-green-500/5");

            const dot = card.querySelector("span.absolute");
            if (dot) dot.remove();

            const btn = card.querySelector(".notificationRead");
            if (btn) {
                const buttonContainer = btn.parentElement;
                btn.remove();
                if (buttonContainer) {
                    const existingReadLabel = buttonContainer.querySelector(".text-green-500");
                    if (!existingReadLabel) {
                        buttonContainer.appendChild(createReadLabel());
                    }
                }
            }
        }

        document.addEventListener("click", (e) => {
            if (e.target.classList.contains("notificationRead")) {
                const card = e.target.closest(".notification-card");
                if (!card) return;

                markCardRead(card);
                updateStats();
            }
        });

        if (markAllBtn) {
            markAllBtn.addEventListener("click", () => {
                document.querySelectorAll(".notification-card.unread").forEach(card => {
                    markCardRead(card);
                });

                updateStats();
            });
        }

        if (notificationList || document.getElementById("totalNotifications") || document.querySelector(".notification-card")) {
            updateStats();
        }
    } catch (error) {
        console.error(error);
    }
});
// ===== NOTIFICATION PAGE END =====
