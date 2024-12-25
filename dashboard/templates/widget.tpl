<div class="widget dashboard-widget">
    <div class="block current-date-block">
        <h3 id="current-dashboard-date">{{$currentDate}}</h3>
        <p>{{$weekLabel}}: <span id="current-dashboard-week">{{$currentWeek}}</span></p>
        <p>{{$currentTimeLabel}}:
            <span id="current-dashboard-time" data-show-clock-label="{{$showClockLabel}}">{{$currentClock}}</span>
        </p>
    </div>

    {{if $blockAppointmentsenable && $appointments|@count}}
        <h3>{{$appointmentsTitle}}</h3>
        <div class="block appointments-block">
            {{foreach from=$appointments item=appointment}}
                <div class="appointment-dashboard-item" data-datetime="{{$appointment.datetime}}" data-alarm="{{$appointment.alarm}}">
                    <strong>
                        {{if $appointment.alarm}}
                            <span class="alarm-icon" title="{{$alarm_enabled}}">🔔</span>
                        {{/if}}
                        {{$appointment.name}}
                    </strong><br>
                    {{$appointment.date}} {{$appointment.time}}
                </div>
            {{/foreach}}
        </div>
    {{/if}}

    {{if $blockTemplatesenable && $templates|@count}}
        <h3>{{$templatesTitle}}</h3>
        <div class="block template-block">
            {{foreach from=$templates item=tpl}}
                <div class="template-item" data-index="{{$tpl.index}}">
                    <span class="template-text" title="{{$tpl.text}}">{{$tpl.text}}</span>
                    <div class="copy-text-container">
                        <a href="javascript:void(0);" class="copy-button"
                           data-template="{{$tpl.index}}">{{$copy_text}}</a>
                    </div>
                </div>
            {{/foreach}}
        </div>
    {{/if}}
        

    <div class="dashboard-settings-link">
        <a href="{{$settingsUrl}}">{{$settings}}</a>
    </div>
</div>
