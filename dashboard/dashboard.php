<?php
/**
 * Name: Dashboard
 * Description: The Dashboard Addon offers a customisable Dashboard in the network sidebar. It displays the current date, week, time, appointments with reminder function and templates. The style of the widget can be selected from various designs.
 * Version: 1.0
 * Author: Jools <https://loma.ml/profile/jools1976>
 * Status: Beta - For Testing Purposes Only
 * Maintainer: None
 */

use Friendica\Core\Hook;
use Friendica\Core\Renderer;
use Friendica\DI;

function dashboard_install()
{
    Hook::register('network_mod_init', 'addon/dashboard/dashboard.php', 'dashboard_network_mod_init');
    Hook::register('addon_settings', 'addon/dashboard/dashboard.php', 'dashboard_addon_settings');
    Hook::register('addon_settings_post', 'addon/dashboard/dashboard.php', 'dashboard_addon_settings_post');
}

function dashboard_uninstall()
{
    Hook::unregister('network_mod_init', 'addon/dashboard/dashboard.php', 'dashboard_network_mod_init');
    Hook::unregister('addon_settings', 'addon/dashboard/dashboard.php', 'dashboard_addon_settings');
    Hook::unregister('addon_settings_post', 'addon/dashboard/dashboard.php', 'dashboard_addon_settings_post');
}

function dashboard_network_mod_init(string &$body)
{
    $userId = DI::userSession()->getLocalUserId();
    if (!$userId || !intval(DI::pConfig()->get($userId, 'dashboard', 'dashboard_addon_enable'))) {
        return;
    }

    $settingsUrl = DI::baseUrl() . '/settings/addons/dashboard';

    $blockAppointmentsEnabled = intval(DI::pConfig()->get($userId, 'dashboard', 'block_appointments_enable', 1));
    $showClockLabel = intval(DI::pConfig()->get($userId, 'dashboard', 'show_clock_label', 0));
    $showAppointmentLabel = intval(DI::pConfig()->get($userId, 'dashboard', 'show_appointment_label', 0));

    $selected_css = DI::pConfig()->get($userId, 'dashboard', 'selected_css', 'dashboard.css');
    $css_path = DI::baseUrl() . '/addon/dashboard/css/' . $selected_css;
    $css_file = __DIR__ . '/css/' . $selected_css;

    if (file_exists($css_file)) {
        DI::page()['htmlhead'] .= '<link rel="stylesheet" type="text/css" href="' . $css_path . '?v=' . time() . '" media="all" />' . "\r\n";
    } else {
        $default_css = 'dashboard.css';
        $default_css_path = DI::baseUrl() . '/addon/dashboard/css/' . $default_css;
        DI::page()['htmlhead'] .= '<link rel="stylesheet" type="text/css" href="' . $default_css_path . '?v=' . time() . '" media="all" />' . "\r\n";
    }

    DI::page()['htmlhead'] .= '<script type="text/javascript" src="' . DI::baseUrl() . '/addon/dashboard/js/dashboard.js"></script>' . "\r\n";

    $copy_text = DI::l10n()->t('Copy Text');
    $copied = DI::l10n()->t('Copied');

    DI::page()['htmlhead'] .= <<<EOT
        <script>
            const dashboardTranslations = {
            copyText: "{$copy_text}",
            copied: "{$copied}"
            };
        </script>
    EOT;

    $timezone = DI::pConfig()->get($userId, 'dashboard', 'dashboard_timezone', date_default_timezone_get());
    $dateFormat = DI::pConfig()->get($userId, 'dashboard', 'dashboard_date_format', 'Y-m-d');
    $timeFormat = 'H:i';
    $currentTime = new DateTime('now', new DateTimeZone($timezone));

    setlocale(LC_TIME, 'de_DE.UTF-8');

    $formatter = new IntlDateFormatter(
        'de_DE',
        IntlDateFormatter::FULL,
        IntlDateFormatter::NONE,
        $timezone,
        IntlDateFormatter::GREGORIAN,
        'EEEE, dd.MM.yyyy'
    );
    $currentDate = $formatter->format($currentTime);

    $currentWeek = $currentTime->format('W');
    $currentClock = $currentTime->format('H:i:s');
    if ($showClockLabel) {
        $currentClock .= ' Uhr';
    }

    $weekLabel = DI::l10n()->t('Week');
    $currentTimeLabel = DI::l10n()->t('Current Time');

    $appointments = [];
    for ($i = 1; $i <= 10; $i++) {
        $appointment_name = trim(DI::pConfig()->get($userId, 'dashboard', 'appointment_name_' . $i, ''));
        $appointment_datetime = trim(DI::pConfig()->get($userId, 'dashboard', 'appointment_datetime_' . $i, ''));
        $appointment_show = intval(DI::pConfig()->get($userId, 'dashboard', 'appointment_show_' . $i, 0));

        if ($appointment_show && !empty($appointment_name) && !empty($appointment_datetime)) {
            try {
                $appointmentDateTime = new DateTime($appointment_datetime, new DateTimeZone($timezone));
                $formattedDate = $appointmentDateTime->format($dateFormat);
                $formattedTime = $appointmentDateTime->format($timeFormat);

                if ($showAppointmentLabel) {
                    $formattedTime .= ' Uhr';
                }

                $appointments[] = [
                    'index' => $i,
                    'name' => htmlspecialchars($appointment_name, ENT_QUOTES, 'UTF-8'),
                    'date' => htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8'),
                    'time' => htmlspecialchars($formattedTime, ENT_QUOTES, 'UTF-8'),
                    'datetime' => $appointmentDateTime->format('Y-m-d\TH:i:s'),
                    'alarm' => intval(DI::pConfig()->get($userId, 'dashboard', 'appointment_alarm_' . $i, 0)),
                ];
            } catch (Exception $e) {
            }
        }
    }

    $blockTemplatesenable = intval(DI::pConfig()->get($userId, 'dashboard', 'block_templates_enable', 1));
    $templates = [];
    for ($i = 1; $i <= 10; $i++) {
        $text = trim(DI::pConfig()->get($userId, 'dashboard', 'template_' . $i, ''));
        $show = intval(DI::pConfig()->get($userId, 'dashboard', 'show_template_' . $i, 0));

        if ($show && !empty($text)) {
            $templates[] = [
                'index' => $i,
                'text' => html_entity_decode($text, ENT_QUOTES, 'UTF-8'),
                'show' => $show,
            ];
        }
    }

    $template = Renderer::getMarkupTemplate('widget.tpl', 'addon/dashboard/');
    $widget = Renderer::replaceMacros($template, [
        '$title' => DI::l10n()->t('Dashboard'),
        '$currentDate' => htmlspecialchars($currentDate, ENT_QUOTES, 'UTF-8'),
        '$currentWeek' => htmlspecialchars($currentWeek, ENT_QUOTES, 'UTF-8'),
        '$currentClock' => htmlspecialchars($currentClock, ENT_QUOTES, 'UTF-8'),
        '$showClockLabel' => $showClockLabel ? 1 : 0,
        '$weekLabel' => $weekLabel,
        '$currentTimeLabel' => $currentTimeLabel,
        '$appointmentsTitle' => DI::l10n()->t('Appointments'),
        '$alarm_enabled' => DI::l10n()->t('Reminder enabled'),
        '$templatesTitle' => DI::l10n()->t('Templates'),
        '$appointments' => $appointments,
        '$blockAppointmentsenable' => $blockAppointmentsEnabled,
        '$blockTemplatesenable' => $blockTemplatesenable,
        '$templates' => $templates,
        '$copy_text' => $copy_text,
        '$copied' => $copied,
        '$settings' => DI::l10n()->t('Settings'),
        '$settingsUrl' => $settingsUrl,
    ]);

    DI::page()['aside'] = $widget . (DI::page()['aside'] ?? '');
}

function dashboard_validateAndSaveAppointments(int $userId, array $post): array
{
    $errors = [];
    $maxAppointments = 10;

    for ($i = 1; $i <= $maxAppointments; $i++) {
        $appointment_name = trim($post['appointment_name_' . $i] ?? '');
        $appointment_datetime = trim($post['appointment_datetime_' . $i] ?? '');
        $appointment_show = isset($post['appointment_show_' . $i]) ? 1 : 0;
        $appointment_alarm = isset($post['appointment_alarm_' . $i]) ? 1 : 0;

        if (!empty($appointment_name) && !empty($appointment_datetime)) {
            try {
                new DateTime($appointment_datetime);
            } catch (Exception $e) {
                $errors[] = DI::l10n()->t(
                    'Invalid date/time for appointment #%1$d: %2$s',
                    $i,
                    $appointment_datetime
                );
                continue;
            }

            DI::pConfig()->set($userId, 'dashboard', 'appointment_name_' . $i, $appointment_name);
            DI::pConfig()->set($userId, 'dashboard', 'appointment_datetime_' . $i, $appointment_datetime);
            DI::pConfig()->set($userId, 'dashboard', 'appointment_show_' . $i, $appointment_show);
            DI::pConfig()->set($userId, 'dashboard', 'appointment_alarm_' . $i, $appointment_alarm);
        } else {
            DI::pConfig()->set($userId, 'dashboard', 'appointment_name_' . $i, '');
            DI::pConfig()->set($userId, 'dashboard', 'appointment_datetime_' . $i, '');
            DI::pConfig()->set($userId, 'dashboard', 'appointment_show_' . $i, 0);
            DI::pConfig()->set($userId, 'dashboard', 'appointment_alarm_' . $i, 0);
        }
    }

    return $errors;
}

function dashboard_validateAndSaveTemplates(int $userId, array $post): array
{
    $errors = [];
    $maxTemplates = 10;

    DI::pConfig()->set($userId, 'dashboard', 'block_templates_enable', !empty($post['block_templates_enable']) ? 1 : 0);

    for ($i = 1; $i <= $maxTemplates; $i++) {
        $template_text = trim($post['template_' . $i] ?? '');
        $template_show = !empty($post['show_template_' . $i]) ? 1 : 0;

        if (stripos($template_text, 'DROP TABLE') !== false) {
            $errors[] = DI::l10n()->t('Potentially dangerous content in template #%d. Not saved.', $i);
            continue;
        }

        if (!empty($template_text)) {
            DI::pConfig()->set($userId, 'dashboard', 'template_' . $i, $template_text);
            DI::pConfig()->set($userId, 'dashboard', 'show_template_' . $i, $template_show);
        } else {
            DI::pConfig()->set($userId, 'dashboard', 'template_' . $i, '');
            DI::pConfig()->set($userId, 'dashboard', 'show_template_' . $i, 0);
        }
    }

    return $errors;
}

function dashboard_addon_settings_post(array $post)
{
    $userId = DI::userSession()->getLocalUserId();
    if (!$userId) {
        return;
    }

    DI::pConfig()->set($userId, 'dashboard', 'dashboard_addon_enable', intval($post['dashboard_addon_enable'] ?? 0));
    DI::pConfig()->set($userId, 'dashboard', 'dashboard_timezone', trim($post['dashboard_timezone'] ?? ''));
    DI::pConfig()->set($userId, 'dashboard', 'dashboard_date_format', trim($post['dashboard_date_format'] ?? ''));
    DI::pConfig()->set($userId, 'dashboard', 'show_clock_label', intval($post['show_clock_label'] ?? 0));
    DI::pConfig()->set($userId, 'dashboard', 'show_appointment_label', intval($post['show_appointment_label'] ?? 0));
    DI::pConfig()->set($userId, 'dashboard', 'block_appointments_enable', !empty($post['block_appointments_enable']) ? 1 : 0);

    DI::pConfig()->set($userId, 'dashboard', 'selected_css', trim($post['selected_css'] ?? 'dashboard.css'));

    $errorsAppointments = dashboard_validateAndSaveAppointments($userId, $post);

    $errorsTemplates = dashboard_validateAndSaveTemplates($userId, $post);

    $allErrors = array_merge($errorsAppointments, $errorsTemplates);

    if (!empty($allErrors)) {
        DI::page()['dashboard_addon_errors'] = $allErrors;
    }
}

function dashboard_addon_settings(array &$data)
{
    $userId = DI::userSession()->getLocalUserId();
    if (!$userId) {
        return;
    }

    DI::page()['htmlhead'] .= '<link rel="stylesheet" type="text/css" href="' . DI::baseUrl() . '/addon/dashboard/css/dashboard_settings.css" media="all" />' . "\r\n";


    DI::page()['htmlhead'] .= '<script type="text/javascript" src="' . DI::baseUrl() . '/addon/dashboard/js/dashboard_settings.js"></script>' . "\r\n";

    $errors = DI::page()['dashboard_addon_errors'] ?? [];

    $errorHtml = '';
    if (!empty($errors)) {
        $errorLines = '';
        foreach ($errors as $err) {
            $errorLines .= '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $errorHtml = '<div class="error-messages"><ul>' . $errorLines . '</ul></div>';
    }

    $dashboard_addon_enable = intval(DI::pConfig()->get($userId, 'dashboard', 'dashboard_addon_enable', 0));
    $selected_css = DI::pConfig()->get($userId, 'dashboard', 'selected_css', 'dashboard.css');
    $dashboard_timezone = DI::pConfig()->get($userId, 'dashboard', 'dashboard_timezone', date_default_timezone_get());
    $dashboard_date_format = DI::pConfig()->get($userId, 'dashboard', 'dashboard_date_format', 'Y-m-d');
    $showClockLabel = intval(DI::pConfig()->get($userId, 'dashboard', 'show_clock_label', 0));
    $showAppointmentLabel = intval(DI::pConfig()->get($userId, 'dashboard', 'show_appointment_label', 0));
    $block_appointments_enable = intval(DI::pConfig()->get($userId, 'dashboard', 'block_appointments_enable', 0));
    $block_templates_enable = intval(DI::pConfig()->get($userId, 'dashboard', 'block_templates_enable', 0));

    $templates = [];
    for ($i = 1; $i <= 10; $i++) {
        $template_text = DI::pConfig()->get($userId, 'dashboard', 'template_' . $i, '');
        $template_show = intval(DI::pConfig()->get($userId, 'dashboard', 'show_template_' . $i, 0));
        if (!empty($template_text)) {
            $templates[] = [
                'index' => $i,
                'text' => $template_text,
                'show' => $template_show,
            ];
        }
    }

    $css_options = [
        'dashboard.css' => 'Default',
        'dashboard_dark.css' => 'Dark',
        'dashboard_light.css' => 'Light',
        'dashboard_modern.css' => 'Modern',
        'dashboard_minimal.css' => 'Minimal',
        'dashboard_retro.css' => 'Retro',
    ];

    $timezones = DateTimeZone::listIdentifiers();
    $timezone_options = array_combine($timezones, $timezones);

    $date_formats = [
        'd.m.Y' => '27.04.2024',
        'Y-m-d' => '2024-04-27',
        'd/m/Y' => '27/04/2024',
        'm/d/Y' => '04/27/2024',
        'd M Y' => '27 Apr 2024',
        'M d, Y' => 'Apr 27, 2024',
    ];

    $appointments = [];
    for ($i = 1; $i <= 10; $i++) {
        $appointment_name = trim(DI::pConfig()->get($userId, 'dashboard', 'appointment_name_' . $i, ''));
        $appointment_datetime = trim(DI::pConfig()->get($userId, 'dashboard', 'appointment_datetime_' . $i, ''));

        if (!empty($appointment_name) && !empty($appointment_datetime)) {
            $appointments[] = [
                'index' => $i,
                'name' => $appointment_name,
                'datetime' => $appointment_datetime,
                'show' => intval(DI::pConfig()->get($userId, 'dashboard', 'appointment_show_' . $i, 0)),
                'alarm' => intval(DI::pConfig()->get($userId, 'dashboard', 'appointment_alarm_' . $i, 0)),
                'name_label' => DI::l10n()->t('Label') . ":",
                'datetime_label' => DI::l10n()->t('Date and Time') . ':',
                'show_label' => DI::l10n()->t('Show this appointment in the Dashboard'),
                'alarm_label' => DI::l10n()->t('Enable alarm for this appointment'),
            ];
        }
    }

    $appointment_label = DI::l10n()->t('Appointment');
    $add_appointment_label = DI::l10n()->t('Add Appointment');
    $delete_label = DI::l10n()->t('Delete');
    $addAppointmentAlert = DI::l10n()->t('You can only add up to 10 appointments.');

    $add_template_label = DI::l10n()->t('Add Template');
    $addTemplateAlert = DI::l10n()->t('You can only add up to 10 templates.');

    $template_label = DI::l10n()->t('Template');

    $dashboardTranslationsJson = json_encode([
        'appointmentLabel' => $appointment_label,
        'addAppointmentLabel' => $add_appointment_label,
        'deleteLabel' => $delete_label,
        'addAppointmentAlert' => $addAppointmentAlert,
        'addTemplateLabel' => $add_template_label,
        'addTemplateAlert' => $addTemplateAlert,
        // Neu:
        'templateLabel' => $template_label,
    ]);

    DI::page()['htmlhead'] .= <<<EOT
        <script>
            const dashboardTranslations = {$dashboardTranslationsJson};
        </script>
    EOT;

    $t = Renderer::getMarkupTemplate('settings.tpl', 'addon/dashboard/');
    $html = Renderer::replaceMacros($t, [
        '$error_messages' => $errorHtml,
        '$dashboard_addon_enable' => $dashboard_addon_enable,
        '$customize_dashboard_style' => DI::l10n()->t('Customize Dashboard Style') . ':',
        '$dashboard_css_field' => [
            'options' => $css_options,
            'selected' => $selected_css,
        ],
        '$customize_dashboard_style_description' => DI::l10n()->t('Select your preferred theme for the Dashboard.'),
        '$description_dashboard_addon' => DI::l10n()->t('The Dashboard Addon provides a customizable dashboard in the network sidebar. It displays the current date, week, time, appointments with reminder functionality, and templates. The style of the widget can be chosen from various designs.'),
        '$general_settings' => DI::l10n()->t('General Settings'),
        '$dashboard_addon_enable_label' => DI::l10n()->t('Activate Dashboard Widget and display it in the Sidebar'),
        '$dashboard_timezone' => ['dashboard_timezone', DI::l10n()->t('Select Timezone') . ':', $dashboard_timezone, DI::l10n()->t('Select preferred timezone for times and appointments.'), $timezone_options],
        '$dashboard_date_format' => ['dashboard_date_format', DI::l10n()->t('Select Date Format') . ':', $dashboard_date_format, DI::l10n()->t('Select preferred date format for times and appointments.'), $date_formats],
        '$showClockLabel' => $showClockLabel,
        '$showClockLabel_label' => DI::l10n()->t('Show "Uhr" after time'),
        '$showAppointmentLabel' => $showAppointmentLabel,
        '$showAppointmentLabel_label' => DI::l10n()->t('Show "Uhr" after appointment times'),
        '$overview' => DI::l10n()->t('Overview'),
        'appointments_label' => DI::l10n()->t('Appointments'),
        '$appointments_description' => DI::l10n()->t('Up to 10 appointments can be displayed in the Dashboard. In addition, a reminder can be activated for the appointment. At the desired time, if the reminder is activated, the appointment will be highlighted in red in the Dashboard.'),
        '$block_appointments_enable' => $block_appointments_enable,
        '$block_appointments_enable_label' => DI::l10n()->t('Show Appointments in the Dashboard'),
        '$appointments' => $appointments,
        '$appointment_label' => DI::l10n()->t('Appointment'),
        'templates_label' => DI::l10n()->t('Templates'),
        '$templates_description' => DI::l10n()->t('With the help of templates, frequently used texts, links, etc. can be saved and displayed in the Dashboard. With a click, the template is copied to the clipboard and can then be pasted anywhere.'),
        '$block_templates_enable' => $block_templates_enable,
        '$block_templates_enable_label' => DI::l10n()->t('Show Templates in the Dashboard'),
        '$templates' => $templates,
        '$template_show_label' => DI::l10n()->t('Show Template in the Dashboard'),
        '$appointment_name_label' => DI::l10n()->t('Label') . ":",
        '$appointment_datetime_label' => DI::l10n()->t('Date and Time') . ':',
        '$appointment_show_label' => DI::l10n()->t('Show this appointment in the Dashboard'),
        '$appointment_alarm_label' => DI::l10n()->t('Enable alarm for this appointment'),
        '$add_appointment_label' => $add_appointment_label,
        '$delete_label' => $delete_label,
        '$add_template_label' => $add_template_label,
        '$template_label' => $template_label,
        '$create_new_appointment_title' => DI::l10n()->t('Create New Appointment'),
        '$create_new_template_title' => DI::l10n()->t('Create New Template'),
        'dashboard_save_settings' => DI::l10n()->t('Save Settings'),
    ]);

    $data = [
        'addon' => 'dashboard',
        'title' => DI::l10n()->t('Dashboard Settings'),
        'html' => $html,
    ];
}