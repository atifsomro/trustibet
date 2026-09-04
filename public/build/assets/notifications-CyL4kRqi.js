document.addEventListener(`DOMContentLoaded`,()=>{try{let e=document.getElementById(`notificationBtn`),t=document.getElementById(`notificationDropdown`),n=document.getElementById(`notificationCount`),r=document.getElementById(`markAllRead`);e?.addEventListener(`click`,e=>{e.stopPropagation(),t?.classList.toggle(`hidden`)}),document.addEventListener(`click`,()=>{t?.classList.add(`hidden`)}),t?.addEventListener(`click`,e=>{e.stopPropagation()}),document.querySelectorAll(`.notification-item`).forEach(e=>{e.addEventListener(`click`,()=>{if(!e.classList.contains(`unread`))return;e.classList.remove(`unread`),e.classList.remove(`bg-green-500/5`);let t=e.querySelector(`.notification-dot`);t&&t.remove();let r=parseInt(n?.textContent||`0`,10)||0;r>0&&(r--,n.textContent=r),r===0&&n?.classList.add(`hidden`)})}),r?.addEventListener(`click`,()=>{document.querySelectorAll(`.notification-item.unread`).forEach(e=>{e.classList.remove(`unread`),e.classList.remove(`bg-green-500/5`);let t=e.querySelector(`.notification-dot`);t&&t.remove()}),n.textContent=`0`,n?.classList.add(`hidden`)})}catch(e){console.error(e)}}),document.addEventListener(`DOMContentLoaded`,()=>{try{let e=document.getElementById(`notificationList`),t=document.getElementById(`markAllReadPage`),n=document.getElementById(`notificationPagination`);if(!n)return;let r=1;function i(){return Array.from(document.querySelectorAll(`.notification-card`))}function a(){let t=i(),a=t.length,o=t.filter(e=>e.classList.contains(`unread`)).length,c=a-o,l=document.getElementById(`totalNotifications`),u=document.getElementById(`unreadNotifications`),d=document.getElementById(`readNotifications`);l&&(l.textContent=a),u&&(u.textContent=o),d&&(d.textContent=c);let f=document.getElementById(`notificationCount`);if(f&&(o>0?(f.classList.remove(`hidden`),f.textContent=o):f.classList.add(`hidden`)),e&&a===0){e.innerHTML=`
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
                `,n&&(n.innerHTML=``);return}s(r)}function o(e){if(!n)return;if(e<=1){n.innerHTML=``;return}let t=[];t.push(`
                <button type="button" class="btn-secondary page-btn ${r===1?`opacity-50 pointer-events-none`:``}" data-action="prev">
                    Previous
                </button>
            `);for(let n=1;n<=e;n++)t.push(`
                    <button type="button" class="page-btn ${n===r?`btn-primary`:`btn-secondary`}" data-page="${n}">
                        ${n}
                    </button>
                `);t.push(`
                <button type="button" class="btn-secondary page-btn ${r===e?`opacity-50 pointer-events-none`:``}" data-action="next">
                    Next
                </button>
            `),n.innerHTML=t.join(``),n.querySelectorAll(`.page-btn`).forEach(t=>{t.addEventListener(`click`,()=>{if(t.dataset.action===`prev`){r>1&&s(r-1);return}if(t.dataset.action===`next`){r<e&&s(r+1);return}let n=Number(t.dataset.page);n&&s(n)})})}function s(e){let t=i(),n=Math.max(1,Math.ceil(t.length/5));r=Math.min(Math.max(e,1),n),t.forEach((e,t)=>{let n=(r-1)*5,i=n+5;e.style.display=t>=n&&t<i?``:`none`}),o(n)}function c(){let e=document.createElement(`div`);return e.className=`mt-5`,e.innerHTML=`<span class="text-sm text-green-500">Read</span>`,e}function l(e){e.classList.remove(`unread`,`bg-green-500/5`);let t=e.querySelector(`span.absolute`);t&&t.remove();let n=e.querySelector(`.notificationRead`);if(n){let e=n.parentElement;n.remove(),e&&(e.querySelector(`.text-green-500`)||e.appendChild(c()))}}document.addEventListener(`click`,e=>{if(e.target.classList.contains(`notificationRead`)){let t=e.target.closest(`.notification-card`);if(!t)return;l(t),a()}}),t&&t.addEventListener(`click`,()=>{document.querySelectorAll(`.notification-card.unread`).forEach(e=>{l(e)}),a()}),(e||document.getElementById(`totalNotifications`)||document.querySelector(`.notification-card`))&&a()}catch(e){console.error(e)}});