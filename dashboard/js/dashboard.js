document.addEventListener("DOMContentLoaded", () => {
    function updateTime() {
        const timeElement = document.getElementById("current-dashboard-time");
        const now = new Date();
        if (timeElement) {
            const hours = String(now.getHours()).padStart(2, "0");
            const minutes = String(now.getMinutes()).padStart(2, "0");
            const seconds = String(now.getSeconds()).padStart(2, "0");
            const showClockLabel = timeElement.dataset.showClockLabel === "1";
            timeElement.textContent = `${hours}:${minutes}:${seconds}${showClockLabel ? " Uhr" : ""}`;
        }

        const dateElement = document.getElementById("current-dashboard-date");
        if (dateElement) {
            const options = { weekday: "long", day: "2-digit", month: "2-digit", year: "numeric" };
            dateElement.textContent = now.toLocaleDateString("de-DE", options);
        }

        const weekElement = document.getElementById("current-dashboard-week");
        if (weekElement) {
            const weekNumber = getWeekNumber(now);
            weekElement.textContent = weekNumber;
        }

        document.querySelectorAll(".appointment-dashboard-item").forEach((item) => {
            const appointmentTime = new Date(item.dataset.datetime);
            const alarmEnabled = item.dataset.alarm === "1";
            if (alarmEnabled && now >= appointmentTime) {
                item.style.color = "red";
                item.classList.add("shake");
            } else {
                item.style.color = "";
                item.classList.remove("shake");
            }
        });
    }

    function getWeekNumber(date) {
        const targetDate = new Date(date.valueOf());
        const dayNumber = (date.getUTCDay() + 6) % 7;
        targetDate.setUTCDate(targetDate.getUTCDate() - dayNumber + 3);
        const firstThursday = targetDate.valueOf();
        targetDate.setUTCMonth(0, 1);
        if (targetDate.getUTCDay() !== 4) {
            targetDate.setUTCMonth(0, 1 + ((4 - targetDate.getUTCDay()) + 7) % 7);
        }
        return Math.ceil((firstThursday - targetDate) / 604800000) + 1;
    }

    setInterval(updateTime, 1000);
    updateTime();

 document.querySelectorAll(".copy-button").forEach(button => {
        button.addEventListener("click", (event) => {
            const templateId = event.target.dataset.template;
            const templateItem = document.querySelector(`.template-item[data-index="${templateId}"] .template-text`);
            if (!templateItem) return;

            const templateText = templateItem.textContent || "";

            if (navigator.clipboard) {
                navigator.clipboard.writeText(templateText).then(() => {
                    const originalText = event.target.textContent;
                    event.target.textContent = dashboardTranslations.copied;
                    setTimeout(() => {
                        event.target.textContent = originalText;
                    }, 2000);
                });
            } else {
                const textarea = document.createElement("textarea");
                textarea.value = templateText;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand("copy");
                const originalText = event.target.textContent;
                event.target.textContent = dashboardTranslations.copied;
                setTimeout(() => {
                    event.target.textContent = originalText;
                }, 2000);
                document.body.removeChild(textarea);
            }
        });
    });
});
