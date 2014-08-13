<?php
class vendas {
    //Controles para o SELECT
    Public $page;
    Public $rows;
    Public $sort;
    Public $order;
    Public $offset;
    Public $total;

    function __construct() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION ['USERID']) and ! isset($_SESSION ['NIVEL'])) {
            include ('../view/login.html');
        } elseif ($_SESSION ['NIVEL'] > 1000) {
            $this->page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $this->rows = isset($_POST['rows']) ? intval($_POST['rows']) : 10;
            $this->sort = isset($_POST['sort']) ? strval($_POST['sort']) : 'name';
            $this->order = isset($_POST['order']) ? strval($_POST['order']) : 'asc';
            $this->offset = ($this->page - 1) * $this->rows;
        }
    }

    Public function get_vendas_grid() {
        //$param -> Recebe uma string pra procurar pelo NOME da loja
        include 'controls/shared/conn.php'; //Toda conexão com o BD principal vem através desta configuração
        $rowsPerPag = 0;

        $rs = mysql_query("SELECT COUNT(*) FROM user");
        $row = mysql_fetch_row($rs);
        $this->total = $row[0];
        $rs = mysql_query("SELECT * FROM user ORDER BY $this->sort $this->order LIMIT $this->offset,$this->rows");

        $items = array();
        $BrGridRow = '';

        while ($row = mysql_fetch_object($rs)) {
            $BrGridRow .= '<tr>'
                    . '<td>' . $row->id . '</td>'
                    . '<td>' . $row->name . '</td>'
                    . '<td>'
                    . '<a href="#" title="alterar">Alterar</a> - '
                    . '<a href="#" title="apagar">apagar</a>'
                    . '</td>'
                    . '</tr>';
            $rowsPerPag++;
        }
        $this->rows = $rowsPerPag;
        return $BrGridRow;
    }

    Public function get_vendas_map() {
        require_once("controls/shared/simpleGMapAPI.php");
        require_once("controls/shared/simpleGMapGeocoder.php");

        ob_start();

        $map = new simpleGMapAPI();
        $geo = new simpleGMapGeocoder();

        $map->setWidth(1000);
        $map->setHeight(390);
        $map->setBackgroundColor('#d0d0d0');
        $map->setMapDraggable(true);
        $map->setDoubleclickZoom(false);
        $map->setScrollwheelZoom(true);

        $map->showDefaultUI(false);
        $map->showMapTypeControl(true, 'DROPDOWN_MENU');
        $map->showNavigationControl(true, 'DEFAULT');
        $map->showScaleControl(true);
        $map->showStreetViewControl(true);

        $map->setZoomLevel(10); // not really needed because showMap is called in this demo with auto zoom
        $map->setInfoWindowBehaviour('MULTIPLE');
        $map->setInfoWindowTrigger('CLICK');

        //MOnta as lojas no MAPA
        $allMarkers = $this->get_markers();
        foreach ($allMarkers as $currentMarker) {
            $map->addMarkerByAddress($currentMarker[0], $currentMarker[1], $currentMarker[2], $currentMarker[3]);
        }
        /* 		
          $opts = array('fillColor'=>'#0000dd', 'fillOpacity'=>0.2, 'strokeColor'=>'#000000', 'strokeOpacity'=>1,
          'strokeWeight'=>2, 'clickable'=>true);
          $map->addCircle(52.0149436, 8.5275128, 1500, "1,5km Umgebung um die Sparrenburg", $opts);

          $opts = array('fillColor'=>'#00dd00', 'fillOpacity'=>0.2, 'strokeColor'=>'#003300', 'strokeOpacity'=>1,
          'strokeWeight'=>2, 'clickable'=>true);
          $map->addRectangle(52.0338, 8.487, 52.0414, 8.502, "Campus Universität Bielefeld", $opts);
         */
        $map->printGMapsJS();
        // showMap with auto zoom enabled
        $map->showMap(true);
        $myStr = ob_get_contents();
        ob_end_clean();
        return $myStr;
    }

    Private function get_infowindow($nomeLoja, $FranquiaLoja, $EndereçoLoja, $cidadeUF, $FaturamentoLoja, $linkClick) {
        return '<div class="detail-store">' .
                '<h3>' . $nomeLoja . '</h3>' .
                '<h4>' . $FranquiaLoja . '</h4>' .
                '<p><i>' . $EndereçoLoja . '</i></p>' .
                '<p><i>' . $cidadeUF . '</i></p>' .
                '<p class="total-store"><b>Faturamento total:</b> ' . $FaturamentoLoja . '</p>' .
                '<a href="' . $linkClick . '" title="ver vendas desta loja">vendas desta loja</a>' .
                '</div>';
    }

}

?>