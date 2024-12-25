document.addEventListener("DOMContentLoaded", () => {
    const maxAppointments = 10;
    const addAppointmentButton = document.getElementById("add-appointment");
    const appointmentsContainer = document.getElementById("appointments-container");
    const appointmentTemplate = document.getElementById("appointment-template");

    if (addAppointmentButton) {
        addAppointmentButton.addEventListener("click", () => {
            const currentAppointments = appointmentsContainer.querySelectorAll(".appointment-fieldset").length;
            if (currentAppointments >= maxAppointments) {
                alert(dashboardTranslations.addAppointmentAlert);
                return;
            }

            let newAppointment = appointmentTemplate.cloneNode(true);
            newAppointment.style.display = "block";
            newAppointment.id = "";

            newAppointment.querySelector(".delete-appointment").addEventListener("click", (event) => {
                const fs = event.target.closest(".appointment-fieldset");
                if (fs) {
                    fs.remove();
                    reindexAppointments();
                }
            });

            appointmentsContainer.insertBefore(newAppointment, appointmentsContainer.firstChild);

            reindexAppointments();
        });
    }

    document.querySelectorAll(".delete-appointment").forEach(button => {
        button.addEventListener("click", (event) => {
            const fs = event.target.closest(".appointment-fieldset");
            if (fs) {
                fs.remove();
                reindexAppointments();
            }
        });
    });

    function reindexAppointments() {
        const fieldsets = appointmentsContainer.querySelectorAll(".appointment-fieldset");
        fieldsets.forEach((fs, idx) => {
            const newIndex = idx + 1;

            const legend = fs.querySelector("legend");
            if (legend) {
                legend.textContent = dashboardTranslations.appointmentLabel + " " + newIndex;
            }

            const nameField = fs.querySelector('[id^="appointment_name_"]');
            if (nameField) {
                nameField.id = "appointment_name_" + newIndex;
                nameField.name = "appointment_name_" + newIndex;
            }

            const dateTimeField = fs.querySelector('[id^="appointment_datetime_"]');
            if (dateTimeField) {
                dateTimeField.id = "appointment_datetime_" + newIndex;
                dateTimeField.name = "appointment_datetime_" + newIndex;
            }

            const showCheck = fs.querySelector('[id^="appointment_show_"]');
            if (showCheck) {
                showCheck.id = "appointment_show_" + newIndex;
                showCheck.name = "appointment_show_" + newIndex;
            }

            const alarmCheck = fs.querySelector('[id^="appointment_alarm_"]');
            if (alarmCheck) {
                alarmCheck.id = "appointment_alarm_" + newIndex;
                alarmCheck.name = "appointment_alarm_" + newIndex;
            }

            const alarmContainer = fs.querySelector('[id^="alarm-container-"]');
            if (alarmContainer) {
                alarmContainer.id = "alarm-container-" + newIndex;
            }
        });
    }

    document.querySelectorAll("[id^=appointment_show_]").forEach(showCheckbox => {
        toggleAlarmVisibility(showCheckbox);
        showCheckbox.addEventListener("change", (ev) => {
            toggleAlarmVisibility(ev.target);
        });
    });

    function toggleAlarmVisibility(showCheckbox) {
        const index = showCheckbox.id.replace("appointment_show_", "");
        const alarmContainer = document.getElementById("alarm-container-" + index);
        if (!alarmContainer) return;

        alarmContainer.style.display = showCheckbox.checked ? "block" : "none";
    }
});



document.addEventListener("DOMContentLoaded", () => {
    const maxTemplates = 10;
    const addTemplateButton = document.getElementById("add-template");
    const templatesContainer = document.getElementById("templates-container");
    const templateTemplate = document.getElementById("template-template");

    if (addTemplateButton) {
        addTemplateButton.addEventListener("click", () => {
            const currentTemplates = templatesContainer.querySelectorAll(".template-fieldset").length;
            if (currentTemplates >= maxTemplates) {
                alert(dashboardTranslations.addTemplateAlert);
                return;
            }

            let newTpl = templateTemplate.cloneNode(true);
            newTpl.style.display = "block";
            newTpl.id = "";

            newTpl.querySelector(".delete-template").addEventListener("click", (event) => {
                const fs = event.target.closest(".template-fieldset");
                if (fs) {
                    fs.remove();
                    reindexTemplates();
                }
            });

            templatesContainer.insertBefore(newTpl, templatesContainer.firstChild);

            reindexTemplates();
        });
    }

    document.querySelectorAll(".delete-template").forEach(btn => {
        btn.addEventListener("click", (event) => {
            const fs = event.target.closest(".template-fieldset");
            if (fs) {
                fs.remove();
                reindexTemplates();
            }
        });
    });

    function reindexTemplates() {
        const fieldsets = templatesContainer.querySelectorAll(".template-fieldset");
        fieldsets.forEach((fs, idx) => {
            const newIndex = idx + 1;

            const legend = fs.querySelector("legend");
            if (legend) {
                legend.textContent = dashboardTranslations.templateLabel + " " + newIndex;
            }

            const textArea = fs.querySelector('[id^="template_"]');
            if (textArea) {
                textArea.id = "template_" + newIndex;
                textArea.name = "template_" + newIndex;
            }

            const showCheckbox = fs.querySelector('[id^="show_template_"]');
            if (showCheckbox) {
                showCheckbox.id = "show_template_" + newIndex;
                showCheckbox.name = "show_template_" + newIndex;
            }
        });
    }
});
