<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Class LTEBootstrap
 * Create Bootstrap components for Laravel 9.
 * AdminLTE2 is used as the CSS framework.
 * 
 */


class LTEBootstrap
{
    /**
     * Generate a Bootstrap button.
     *
     * @param string $text
     * @param string $url
     * @param string $type
     * @param array $options
     * @return HtmlString
     */
    public static function Button(string $text, string $url, string $type = 'primary', array $options = []): HtmlString
    {
        $class = isset($options['class']) ? $options['class'] . ' ' : '';
        $class .= 'btn btn-' . $type;
        $attributes = array_merge($options, ['class' => $class]);
    
        $htmlAttributes = self::buildAttributes($attributes);
        $html = \Illuminate\Support\Facades\HTML::link($url, $text, $attributes);
    
        return new HtmlString($html);
    }    

    /**
     * Generate a Bootstrap table.
     *
     * @param array $headers
     * @param array $rows
     * @param array $options
     * @return HtmlString
     */  
     public static function Table(array $headers, array $rows, array $options = []): HtmlString
     {
         $tableHeaders = '';
         foreach ($headers as $header) {
             $tableHeaders .= '<th>' . $header . '</th>';
         }
     
         $tableRows = '';
         foreach ($rows as $row) {                       
             $tableRows .= '<tr>' . $row . '</tr>';
         }
     
         $tableClass = isset($options['class']) ? $options['class'] . ' ' : '';
         $tableClass .= 'table table-striped table-hover shadow border border-gray-300';
         $attributes = array_merge($options, ['class' => $tableClass]);
     
         $tableHtml = '<table ' . self::buildAttributes($attributes) . '>';
         $tableHtml .= '<thead><tr>' . $tableHeaders . '</tr></thead>';
         $tableHtml .= '<tbody>' . $tableRows . '</tbody>';
         $tableHtml .= '</table>';
     
         return new HtmlString($tableHtml);
     }
     

     public static function modal(array $options = []): HtmlString
     {
         $modalId = $options['modal_id'] ?? '';
         $modalTitle = $options['modal_title'] ?? '';
         $modalContent = $options['modal_content'] ?? '';
         $footerButtons = $options['footer_buttons'] ?? [];
         $modalClass = $options['modal_class'] ?? '';
         $footer = $options['footer'] ?? false;
     
         $modalHtml = '<div class="modal fade ' . $modalClass . '" id="' . $modalId . '" tabindex="-1" role="dialog" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">';
         $modalHtml .= '<div class="modal-dialog" role="document">';
         $modalHtml .= '<div class="modal-content">';
         $modalHtml .= self::modalHeader($modalTitle, $modalId);
         $modalHtml .= '<div class="modal-body">' . $modalContent . '</div>';
     
         if ($footer) {
            $modalHtml .= self::modalFooter($footerButtons);
        }
    
        $modalHtml .= '     </div>';
        $modalHtml .= '   </div>';
        $modalHtml .= '</div>';
    
        return new HtmlString($modalHtml);
     }
     

     
     private static function openModal(array $attributes = []): HtmlString
     {
         $html = '<div ' . self::buildAttributes($attributes) . '>';
     
         return new HtmlString($html);
     }
     
     private static function modalHeader(string $title, string $id = '', string $type = ''): HtmlString
    {
        $html = '<div class="modal-header ';
        if ($type === 'danger') {
            $html .= 'bg-danger';
        } elseif ($type === 'success') {
            $html .= 'bg-success';
        }
        $html .= '">';
        $html .= '<h5 class="modal-title" id="' . $id . '">' . $title . '</h5>';
        $html .= '<button type="button" class="close" data-dismiss="modal" aria-label="Close">';
        $html .= '  <span aria-hidden="true">&times;</span>';
        $html .= '</button>';
        $html .= '</div>';

        return new HtmlString($html);
    }

     
     private static function modalBody(string $body): HtmlString
     {
         $html = '<div class="modal-body">' . $body . '</div>';
     
         return new HtmlString($html);
     }
     
    private static function modalFooter(array $buttons): HtmlString
    {
        $html = '<div class="modal-footer">';
        foreach ($buttons as $button) {
            $buttonClass = isset($button['class']) ? $button['class'] . ' ' : 'btn-secondary ';
          
            $buttonAttributes = [
                'class' => 'btn ' . $buttonClass,
                'id' => $button['id'] ? $button['id'] : '',
                'type' => $button['type'] ?? 'button'
            ];
            if (isset($button['dismiss']) && $button['dismiss']) {
                $buttonAttributes['data-dismiss'] = 'modal';
            }
            $buttonHtml = '<button ' . self::buildAttributes($buttonAttributes) . '>' . $button['label'] . '</button>';
            $html .= $buttonHtml;
        }
        $html .= '</div>';

        return new HtmlString($html);
    }

    private static function closeModal(): HtmlString
    {
        $html = '</div></div></div>';

        return new HtmlString($html);
    }


    /**
     * Generate a Bootstrap card.
     *
     * @param string $title
     * @param string $body
     * @param string|null $footer
     * @param array $options
     * @return HtmlString
     */
    public static function card(string $title, string $body, ?string $footer = null, array $options = []): HtmlString
    {
        $cardOptions = [];
        $cardOptions['class'] = 'card ' . ($options['class'] ?? '');

        $cardHtml = '<div ' . \Illuminate\Support\Facades\HTML::attributes($cardOptions) . '>';
        $cardHtml .= '<div class="card-header">' . $title . '</div>';
        $cardHtml .= '<div class="card-body">' . $body . '</div>';
        if ($footer !== null) {
            $cardHtml .= '<div class="card-footer">' . $footer . '</div>';
        }
        $cardHtml .= '</div>';

        return new HtmlString($cardHtml);
    }

    /**
     * Generate a Bootstrap small box.
     *
     * @param string $bgColor
     * @param string $value
     * @param string $supValue
     * @param string $text
     * @param string $icon
     * @param string $link
     * @return HtmlString
     */
    public static function smallBox(string $bgColor, string $value, string $supValue, string $text, string $icon, string $link): HtmlString
    {
        $boxHtml = '<div class="small-box ' . $bgColor . '">';
        $boxHtml .= '<div class="inner">';
        $boxHtml .= '<h3>' . $value . '<sup style="font-size: 20px">' . $supValue . '</sup></h3>';
        $boxHtml .= '<p>' . $text . '</p>';
        $boxHtml .= '</div>';
        $boxHtml .= '<div class="icon">';
        $boxHtml .= '<i class="' . $icon . '"></i>';
        $boxHtml .= '</div>';
        $boxHtml .= '<a href="' . $link . '" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>';
        $boxHtml .= '</div>';

        return new HtmlString($boxHtml);
    }

    /**
     * Generate a Bootstrap info box.
     *
     * @param string $text
     * @param string $number
     * @return HtmlString
     */
    public static function infoBox(string $text, string $number): HtmlString
    {
        $boxHtml = '<div class="info-box">';
        $boxHtml .= '<span class="info-box-icon bg-info"><i class="far fa-envelope"></i></span>';
        $boxHtml .= '<div class="info-box-content">';
        $boxHtml .= '<span class="info-box-text">' . $text . '</span>';
        $boxHtml .= '<span class="info-box-number">' . $number . '</span>';
        $boxHtml .= '</div>';
        $boxHtml .= '</div>';

        return new HtmlString($boxHtml);
    }

    /**
     * Generate a Bootstrap chart card.
     *
     * @param string $title
     * @param string $tab1Name
     * @param string $tab2Name
     * @param string $tab1Content
     * @param string $tab2Content
     * @usage:  <?= \App\Helpers\BootstrapLTEHelper::chartCard('Sales', 'Area', 'Donut', '<canvas id="revenue-chart-canvas" height="300" style="height: 300px"></canvas>', '<canvas id="sales-chart-canvas" height="300" style="height: 300px"></canvas>'); ?>
     * @return HtmlString
     */
    public static function pieChartCard(string $title, string $tab1Name, string $tab2Name, string $tab1Content, string $tab2Content): HtmlString
    {
        $cardHtml = '<div class="card">';
        $cardHtml .= '<div class="card-header">';
        $cardHtml .= '<h3 class="card-title">';
        $cardHtml .= '<i class="fas fa-chart-pie mr-1"></i>' . $title;
        $cardHtml .= '</h3>';
        $cardHtml .= '<div class="card-tools">';
        $cardHtml .= '<ul class="nav nav-pills ml-auto">';
        $cardHtml .= '<li class="nav-item">';
        $cardHtml .= '<a class="nav-link active" href="#revenue-chart" data-toggle="tab">' . $tab1Name . '</a>';
        $cardHtml .= '</li>';
        $cardHtml .= '<li class="nav-item">';
        $cardHtml .= '<a class="nav-link" href="#sales-chart" data-toggle="tab">' . $tab2Name . '</a>';
        $cardHtml .= '</li>';
        $cardHtml .= '</ul>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="card-body">';
        $cardHtml .= '<div class="tab-content p-0">';
        $cardHtml .= '<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px">';
        $cardHtml .= $tab1Content;
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px">';
        $cardHtml .= $tab2Content;
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';

        return new HtmlString($cardHtml);
    }

    /**
     * Generate a Bootstrap direct chat card.
     *
     * @param string $title
     * @param string $badgeValue
     * @return HtmlString
     */
    public static function directChatCard(string $title, string $badgeValue): HtmlString
    {
        $cardHtml = '<div class="card direct-chat direct-chat-primary">';
        $cardHtml .= '<div class="card-header">';
        $cardHtml .= '<h3 class="card-title">' . $title . '</h3>';
        $cardHtml .= '<div class="card-tools">';
        $cardHtml .= '<span title="' . $badgeValue . ' New Messages" class="badge badge-primary">' . $badgeValue . '</span>';
        $cardHtml .= '<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>';
        $cardHtml .= '<button type="button" class="btn btn-tool" title="Contacts" data-widget="chat-pane-toggle"><i class="fas fa-comments"></i></button>';
        $cardHtml .= '<button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="card-body">';
        $cardHtml .= '<div class="direct-chat-messages">';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="direct-chat-contacts">';
        $cardHtml .= '<ul class="contacts-list">';
        $cardHtml .= '<!-- contacts go here -->';
        $cardHtml .= '</ul>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="card-footer">';
        $cardHtml .= '<form action="#" method="post">';
        $cardHtml .= '<div class="input-group">';
        $cardHtml .= '<input type="text" name="message" placeholder="Type Message ..." class="form-control" />';
        $cardHtml .= '<span class="input-group-append"><button type="button" class="btn btn-primary">Send</button></span>';
        $cardHtml .= '</div>';
        $cardHtml .= '</form>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';

        return new HtmlString($cardHtml);
    }

    /**
     * A ToDo List feature
     */
    public static function todoList(): HtmlString
    {
        $itemsPerPage = 5; // Change this to adjust the number of items per page
        $currentPage = request()->get('page') ?? 1;

        $items = DB::table('todos')->paginate($itemsPerPage, ['*'], 'page', $currentPage);

        $listHtml = '<div class="card">';
        $listHtml .= '<div class="card-header">';
        $listHtml .= '<h3 class="card-title"><i class="ion ion-clipboard mr-1"></i>To Do List</h3>';
        $listHtml .= '<div class="card-tools">';
        $listHtml .= $items->links('pagination::bootstrap-4');
        $listHtml .= '</div>';
        $listHtml .= '</div>';
        $listHtml .= '<div class="card-body">';
        $listHtml .= '<ul class="todo-list" data-widget="todo-list">';
        
        foreach ($items as $item) {
            $listHtml .= '<li>';
            $listHtml .= '<span class="handle">';
            $listHtml .= '<i class="fas fa-ellipsis-v"></i>';
            $listHtml .= '<i class="fas fa-ellipsis-v"></i>';
            $listHtml .= '</span>';
            $listHtml .= '<div class="icheck-primary d-inline ml-2">';
            $listHtml .= '<input type="checkbox" value="" name="todo' . $item->id . '" id="todoCheck' . $item->id . '">';
            $listHtml .= '<label for="todoCheck' . $item->id . '">' . $item->description . '</label>';
            $listHtml .= '</div>';
            $listHtml .= '<small class="badge badge-danger"><i class="far fa-clock"></i> ' . $item->estimated_time . '</small>';
            $listHtml .= '<div class="tools">';
            $listHtml .= '<i class="fas fa-edit"></i>';
            $listHtml .= '<i class="fas fa-trash-o"></i>';
            $listHtml .= '</div>';
            $listHtml .= '</li>';
        }
        
        $listHtml .= '</ul>';
        $listHtml .= '</div>';
        $listHtml .= '<div class="card-footer clearfix">';
        $listHtml .= '<button type="button" class="btn btn-primary float-right">';
        $listHtml .= '<i class="fas fa-plus"></i> Add item';
        $listHtml .= '</button>';
        $listHtml .= '</div>';
        $listHtml .= '</div>';

        return new HtmlString($listHtml);
    }

    /**
     * Generate a Bootstrap card with a map.
     */
    public static function visitorsCard(string $title, string $tab1Name, string $tab2Name, string $tab1Content, string $tab2Content): HtmlString
    {
        $cardHtml = '<div class="card bg-gradient-primary">';
        $cardHtml .= '<div class="card-header border-0">';
        $cardHtml .= '<h3 class="card-title">';
        $cardHtml .= '<i class="fas fa-map-marker-alt mr-1"></i>' . $title;
        $cardHtml .= '</h3>';
        $cardHtml .= '<div class="card-tools">';
        $cardHtml .= '<button type="button" class="btn btn-primary btn-sm daterange" title="Date range">';
        $cardHtml .= '<i class="far fa-calendar-alt"></i>';
        $cardHtml .= '</button>';
        $cardHtml .= '<button type="button" class="btn btn-primary btn-sm" data-card-widget="collapse" title="Collapse">';
        $cardHtml .= '<i class="fas fa-minus"></i>';
        $cardHtml .= '</button>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="card-body">';
        $cardHtml .= '<div class="tab-content p-0">';
        $cardHtml .= '<div class="chart tab-pane active" id="map-chart" style="position: relative; height: 300px">';
        $cardHtml .= $tab1Content;
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px">';
        $cardHtml .= $tab2Content;
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="card-footer bg-transparent">';
        $cardHtml .= '<div class="row">';
        $cardHtml .= '<div class="col-4 text-center">';
        $cardHtml .= '<div id="sparkline-1"></div>';
        $cardHtml .= '<div class="text-white">Visitors</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="col-4 text-center">';
        $cardHtml .= '<div id="sparkline-2"></div>';
        $cardHtml .= '<div class="text-white">Online</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="col-4 text-center">';
        $cardHtml .= '<div id="sparkline-3"></div>';
        $cardHtml .= '<div class="text-white">Sales</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
    
        return new HtmlString($cardHtml);
    }

    /**
     * Generate a Bootstrap Sales Graph card.
     *
     * @param string $title
     * @param string $chartId
     * @param string $chartStyle
     * @param string $chartClass
     * @useage <?= \App\Helpers\BootstrapLTEHelper::salesGraphCard('Sales Graph', 'line-chart', 'min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;', 'chart'); ?>
     * @return HtmlString
     */
    public static function salesGraphCard(string $title, string $chartId, string $chartStyle, string $chartClass): HtmlString
    {
        $cardHtml = '<div class="card bg-gradient-info">';
        $cardHtml .= '<div class="card-header border-0">';
        $cardHtml .= '<h3 class="card-title">';
        $cardHtml .= '<i class="fas fa-th mr-1"></i>' . $title;
        $cardHtml .= '</h3>';
        $cardHtml .= '<div class="card-tools">';
        $cardHtml .= '<button type="button" class="btn bg-info btn-sm" data-card-widget="collapse">';
        $cardHtml .= '<i class="fas fa-minus"></i>';
        $cardHtml .= '</button>';
        $cardHtml .= '<button type="button" class="btn bg-info btn-sm" data-card-widget="remove">';
        $cardHtml .= '<i class="fas fa-times"></i>';
        $cardHtml .= '</button>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="card-body">';
        $cardHtml .= '<canvas class="chart ' . $chartClass . '" id="' . $chartId . '" style="' . $chartStyle . '"></canvas>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';

        return new HtmlString($cardHtml);
    }

    /**
     * Generate a card with knob charts in the footer.
     *
     * @param string $text1
     * @param string $text2
     * @param string $text3
     * @param int $value1
     * @param int $value2
     * @param int $value3
     * @useage <?= \App\Helpers\BootstrapLTEHelper::knobCard('New Visitors', 'Online', 'Sales', 1500, 89, 200); ?>
     * @return HtmlString
     */
    public static function knobCard(string $text1, string $text2, string $text3, int $value1, int $value2, int $value3): HtmlString
    {
        $cardHtml = '<div class="card">';
        $cardHtml .= '<div class="card-footer bg-transparent">';
        $cardHtml .= '<div class="row">';
        $cardHtml .= '<div class="col-4 text-center">';
        $cardHtml .= '<input type="text" class="knob" data-readonly="true" value="' . $value1 . '" data-width="60" data-height="60" data-fgColor="#39CCCC" />';
        $cardHtml .= '<div class="text-white">' . $text1 . '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="col-4 text-center">';
        $cardHtml .= '<input type="text" class="knob" data-readonly="true" value="' . $value2 . '" data-width="60" data-height="60" data-fgColor="#39CCCC" />';
        $cardHtml .= '<div class="text-white">' . $text2 . '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="col-4 text-center">';
        $cardHtml .= '<input type="text" class="knob" data-readonly="true" value="' . $value3 . '" data-width="60" data-height="60" data-fgColor="#39CCCC" />';
        $cardHtml .= '<div class="text-white">' . $text3 . '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';

        return new HtmlString($cardHtml);
    }

    /**
     * Generate a Bootstrap calendar card.
     *
     * @param string $title
     * @param string $id
     * @useage <?= \App\Helpers\BootstrapLTEHelper::calendarCard('Calendar', 'calendar'); ?>
     * @return HtmlString
     */
    public static function calendarCard(string $title, string $id): HtmlString
    {
        $cardHtml = '<div class="card bg-gradient-success">';
        $cardHtml .= '<div class="card-header border-0">';
        $cardHtml .= '<h3 class="card-title">';
        $cardHtml .= '<i class="far fa-calendar-alt"></i>' . $title;
        $cardHtml .= '</h3>';
        $cardHtml .= '<div class="card-tools">';
        $cardHtml .= '<div class="btn-group">';
        $cardHtml .= '<button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown" data-offset="-52">';
        $cardHtml .= '<i class="fas fa-bars"></i>';
        $cardHtml .= '</button>';
        $cardHtml .= '<div class="dropdown-menu" role="menu">';
        $cardHtml .= '<a href="#" class="dropdown-item">Add new event</a>';
        $cardHtml .= '<a href="#" class="dropdown-item">Clear events</a>';
        $cardHtml .= '<div class="dropdown-divider"></div>';
        $cardHtml .= '<a href="#" class="dropdown-item">View calendar</a>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<button type="button" class="btn btn-success btn-sm" data-card-widget="collapse"><i class="fas fa-minus"></i></button>';
        $cardHtml .= '<button type="button" class="btn btn-success btn-sm" data-card-widget="remove"><i class="fas fa-times"></i></button>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';
        $cardHtml .= '<div class="card-body pt-0">';
        $cardHtml .= '<div id="' . $id . '" style="width: 100%"></div>';
        $cardHtml .= '</div>';
        $cardHtml .= '</div>';

        return new HtmlString($cardHtml);
    }


    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array  $attributes
     * @return string
     */
    private static function buildAttributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            if (!is_null($value)) {
                $html .= $key . '="' . e($value, false) . '" ';
            }
        }

        return trim($html);
    }

}
