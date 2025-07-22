{if $disabled_new_field}
{literal}
    <script type="text/javascript">
        CRM.$(function($) {
            var disabled_new_field_message = '{/literal}{$disabled_new_field_message}{literal}';
            var current_row_size = '{/literal}{$current_row_size|default:0}{literal}';
            var max_row_size = '{/literal}{$max_row_size|default:65535}{literal}';
            var row_size_percent = '{/literal}{$row_size_percent|default:0}{literal}';

            // Disable the new field button
            $('#newCustomField').css({
                "color": "currentColor",
                "cursor": "not-allowed",
                "opacity": "0.5",
                "text-decoration": "none"
            });
            $('#newCustomField').attr('href', 'javascript:void(0);');

            // Create enhanced warning message with progress indicator
            var warningHtml = '<div class="messages status" style="border-left: 4px solid #d32f2f; background-color: #ffebee; padding: 15px; margin: 15px 0;">' +
                '<div style="display: flex; align-items: center; margin-bottom: 10px;">' +
                '<i class="crm-i fa-exclamation-triangle" style="color: #d32f2f; margin-right: 8px; font-size: 18px;"></i>' +
                '<strong style="color: #d32f2f;">Row Size Limit Reached</strong>' +
                '</div>' +
                '<p style="margin: 8px 0; font-size: 14px;">' + disabled_new_field_message + '</p>' +
                '<div style="margin-top: 12px;">' +
                '<div style="background-color: #f5f5f5; border-radius: 4px; padding: 3px; margin-bottom: 5px;">' +
                '<div style="background-color: #d32f2f; height: 12px; border-radius: 2px; width: ' + row_size_percent + '%; transition: width 0.3s ease;"></div>' +
                '</div>' +
                '<small style="color: #666; font-size: 12px;">Usage: ' + row_size_percent + '% (' + current_row_size + ' / ' + max_row_size + ' bytes)</small>' +
                '</div>' +
                '</div><br/>';

            $(warningHtml).insertBefore('.action-link');
        });
    </script>
{/literal}
{/if}
