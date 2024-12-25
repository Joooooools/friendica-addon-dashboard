<p>{{$description_dashboard_addon}}</p>
<br>

<fieldset>
    <legend>{{$general_settings}}</legend>

    <p>
        <input type="checkbox" id="dashboard_addon_enable" name="dashboard_addon_enable" value="1" {{if $dashboard_addon_enable}}checked{{/if}}>
        <label for="dashboard_addon_enable">{{$dashboard_addon_enable_label}}</label>
    </p><br>

    <p>
        <input type="checkbox" id="block_appointments_enable" name="block_appointments_enable" value="1" {{if $block_appointments_enable}}checked{{/if}}>
        <label for="block_appointments_enable">{{$block_appointments_enable_label}}</label>
    </p>

    <p>
        <input type="checkbox" id="block_templates_enable" name="block_templates_enable" value="1" {{if $block_templates_enable}}checked{{/if}}>
        <label for="block_templates_enable">{{$block_templates_enable_label}}</label>
    </p><br>

    <p>{{include file="field_select.tpl" field=$dashboard_timezone}}</p>
    <p>{{include file="field_select.tpl" field=$dashboard_date_format}}</p>
    <br>

    <p>
        <input type="checkbox" id="show_clock_label" name="show_clock_label" value="1" {{if $showClockLabel}}checked{{/if}}>
        <label for="show_clock_label">{{$showClockLabel_label}}</label>
    </p>
    <p>
        <input type="checkbox" id="show_appointment_label" name="show_appointment_label" value="1" {{if $showAppointmentLabel}}checked{{/if}}>
        <label for="show_appointment_label">{{$showAppointmentLabel_label}}</label>
    </p><br>

    <p>
        <label for="selected_css">{{$customize_dashboard_style}}</label>
        <select id="selected_css" name="selected_css" class="form-control">
            {{foreach from=$dashboard_css_field.options key=css_file item=css_label}}
                <option value="{{$css_file}}" {{if $css_file == $dashboard_css_field.selected}}selected="selected"{{/if}}>{{$css_label}}</option>
            {{/foreach}}
        </select>
    </p>
    <p>{{$customize_dashboard_style_description}}</p>
    <br>
    <p class="dashboard_style_right">
        <input type="submit" class="btn btn-primary" value="{{$dashboard_save_settings}}">
    </p>
</fieldset>

<fieldset>
    <legend>{{$appointments_label}}</legend>
    <p>{{$appointments_description}}</p>
    <br>
    <p><button type="button" id="add-appointment" class="btn btn-primary">
        {{$add_appointment_label}}
    </button></p>
    <br>

    <fieldset id="appointment-template" class="appointment-fieldset" style="display: none;">
        <legend>{{$appointment_label}} __INDEX__</legend>
        <p>
            <label for="appointment_name___INDEX__">{{$appointment_name_label}}</label><br>
            <input type="text" id="appointment_name___INDEX__" name="appointment_name___INDEX__" value="" class="form-control">
        </p>
        <p>
            <label for="appointment_datetime___INDEX__">{{$appointment_datetime_label}}</label><br>
            <input type="datetime-local" id="appointment_datetime___INDEX__" name="appointment_datetime___INDEX__" value="" class="form-control">
        </p>
        <p class="dashboard_row">
            <span>
                <input type="checkbox" id="appointment_show___INDEX__" name="appointment_show___INDEX__" value="1">
                <label for="appointment_show___INDEX__">{{$appointment_show_label}}</label>
            </span>
            <span>
                <a href="javascript:void(0);" class="delete-appointment delete-link">{{$delete_label}}</a>
            </span>
        </p>
        <p>
            <input type="checkbox" id="appointment_alarm___INDEX__" name="appointment_alarm___INDEX__" value="1">
            <label for="appointment_alarm___INDEX__">{{$appointment_alarm_label}} 🔔</label>
        </p><br><hr><br>
    </fieldset>

    <div id="appointments-container">
        {{foreach from=$appointments item=appointment}}
            <fieldset class="appointment-fieldset">
                <legend>{{$appointment_label}} {{$appointment.index}}</legend>
                <p>
                    <label for="appointment_name_{{$appointment.index}}">{{$appointment.name_label}}</label><br>
                    <input type="text"
                           id="appointment_name_{{$appointment.index}}"
                           name="appointment_name_{{$appointment.index}}"
                           value="{{$appointment.name}}"
                           class="form-control">
                </p>
                <p>
                    <label for="appointment_datetime_{{$appointment.index}}">{{$appointment.datetime_label}}</label><br>
                    <input type="datetime-local"
                           id="appointment_datetime_{{$appointment.index}}"
                           name="appointment_datetime_{{$appointment.index}}"
                           value="{{$appointment.datetime}}"
                           class="form-control">
                </p>
                <p class="dashboard_row">
                    <span>
                        <input type="checkbox"
                            id="appointment_show_{{$appointment.index}}"
                            name="appointment_show_{{$appointment.index}}"
                            value="1"
                            {{if $appointment.show}}checked{{/if}}>
                        <label for="appointment_show_{{$appointment.index}}">{{$appointment.show_label}}</label>
                    </span>
                    <span>
                        <a href="javascript:void(0);" class="delete-appointment delete-link">{{$delete_label}}</a>
                    </span>
                </p>    
                <div class="alarm-container" id="alarm-container-{{$appointment.index}}">
                    <input type="checkbox"
                           id="appointment_alarm_{{$appointment.index}}"
                           name="appointment_alarm_{{$appointment.index}}"
                           value="1"
                           {{if $appointment.alarm}}checked{{/if}}>
                    <label for="appointment_alarm_{{$appointment.index}}">
                        {{$appointment.alarm_label}} 🔔
                    </label>
                </div>
                <hr><br>
            </fieldset>
        {{/foreach}}
    </div>
    <p class="dashboard_style_right">
        <input type="submit" class="btn btn-primary" value="{{$dashboard_save_settings}}">
    </p>
</fieldset>

<fieldset>
    <legend>{{$templates_label}}</legend>
    <p>{{$templates_description}}</p>
    <br>
    <p><button type="button" id="add-template" class="btn btn-primary">
        {{$add_template_label}}
    </button></p>
    <br>

    <fieldset id="template-template" class="template-fieldset" style="display: none;">
        <legend>{{$template_label}} __INDEX__</legend>

        <p>
            <textarea id="template___INDEX__"
                      name="template___INDEX__"
                      rows="5" cols="60"
                      maxlength="10000"
                      class="form-control"></textarea>
        </p>
        <p class="dashboard_row">
            <span>
                <input type="checkbox" id="show_template___INDEX__" name="show_template___INDEX__" value="1">
                <label for="show_template___INDEX__">{{$template_show_label}}</label>
            </span>
            <span>
                        <a href="javascript:void(0);" class="delete-template delete-link">{{$delete_label}}</a>
            </span>
        </p>
        <br><hr><br>
    </fieldset>

    <div id="templates-container">
        {{foreach from=$templates item=tpl}}
            <fieldset class="template-fieldset">
                <legend>{{$template_label}} {{$tpl.index}}</legend>
                <p>
                    <textarea id="template_{{$tpl.index}}"
                              name="template_{{$tpl.index}}"
                              rows="5"
                              cols="60"
                              maxlength="10000"
                              class="form-control">{{trim($tpl.text)}}</textarea>
                </p>
                <p class="dashboard_row">    
                    <span>
                        <input type="checkbox"
                            id="show_template_{{$tpl.index}}"
                            name="show_template_{{$tpl.index}}"
                            value="1"
                            {{if $tpl.show}}checked{{/if}}>
                        <label for="show_template_{{$tpl.index}}">
                            {{$template_show_label}}
                        </label>
                    </span>
                    <span class="dashboard_style_right">
                        <a href="javascript:void(0);" class="delete-template delete-link">{{$delete_label}}</a>
                    </span>
                </p>
            </fieldset>
            <hr>
        {{/foreach}}
    </div>
 </fieldset>