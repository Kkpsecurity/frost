<?php


/**
 * @param $data
 * @param array $param
 * @return string
 */
function UIModal($data, $param=[]) {
    $modal          = '';

    $modal_id       = '';
    $modal_type     = '';
    $modal_title    = '';
    $footer         = '';
    $effect         = '';

    extract(array_merge([
        'modal_id'      => '',
        'modal_type'    => 'default',
        'footer'        => true,
        'modal_title'   => 'Modal Title',
        'effect'        => 'fade'
    ], $param));

    $modal = "<!-- BEGIN MODAL MARKUP -->";

    /**
     * Modal Open Div
     */
    $modal.= "<div
        class=\"modal " . $effect . " clearfix\" id=\"" .  $modal_id . "\"
        tabindex=\"-1\"
        role=\"dialog\"
        aria-labelledby=\"" .  underscore($modal_id) . "Label\"
        aria-hidden=\"true\">\n";

    $modal.= "    <div class=\"modal-dialog\">\n";
    $modal.= "        <div class=\"modal-content clearfix\">\n";

    $modal.= "<div class=\"modal-header bg-" . $modal_type . "\">
        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
        <h4 class=\"modal-title\" id=\"modalLabel\">" . $modal_title . "</h4>
    </div>";

    $modal.= "<div class=\"modal-body\" style='padding: 20px;'>
        <p>" . $data . "</p>
    </div>";

    if($footer === true) {
        $modal.= "<div class=\"modal-footer \" style='height: 60px; padding: 20px;'>
            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Cancel</button>
            <button type=\"button\" class=\"btn btn-" . $modal_type . "\" id=\"submit-" . strtolower($modal_id) . "\">Process</button>
        </div>";
    }







































    $modal.= "        </div>";
    $modal.= "    </div>";
    $modal.= "</div><!-- END MODAL MARKUP -->\n";

    return $modal;
}
