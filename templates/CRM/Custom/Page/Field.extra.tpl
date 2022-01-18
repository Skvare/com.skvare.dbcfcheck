{if $disabled_new_field}
{literal}
    <script type="text/javascript">
        CRM.$(function($) {
            var disabled_new_field_message =
                '{/literal}{$disabled_new_field_message}{literal}';
            $('#newCustomField').css(
                {
                    "color":"currentColor",
                    "cursor":"not-allowed",
                    "opacity": "0.5",
                    "text-decoration": "none"
                }
            );
            $('#newCustomField').attr('href', 'javascript:void(0);');
            $('<span style="color: red;">'+disabled_new_field_message+'</span></br>').insertBefore('#newCustomField');
        });
    </script>
{/literal}
{/if}