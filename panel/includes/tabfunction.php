<?php
function table($result,$query) {
    $result->fetch_array( MYSQLI_ASSOC );
    echo '<form action="/moodle/blocks/filtered_reporting/panel/export/xls.php"  method="POST">';
    echo '<table style="max-width:100%;
    margin: auto;
    border: 2px solid black;">';
    tableHead( $result );
    tableBody( $result );
    echo '</table>';
    echo '<br>';
    echo '<div class="container"><div class="col-sm-12" align="center">';
    echo '<input type="submit" name="SubmitButton" class="btn btn-success" value="Télécharger Excel" />';
    echo '<input type="hidden" name="date" id="hiddenField" value="'.$query.'"/>';
    echo '<br>';
    echo '<a href="http://localhost/moodle/">Accueil<a/>';
    echo "</div></div>";
}

function tableHead( $result ) {
    echo '<thead style="max-width:100%;
    margin: auto;
    border: 2px solid black;">';
    foreach ( $result as $x ) {
    echo '<tr>';
    foreach ( $x as $k => $y ) {
        echo '<th>' . ucfirst( $k ) . '</th>';
    }
    echo '</tr>';
    break;
    }
    echo '</thead>';
}

function tableBody( $result ) {
    echo '<tbody>';
    foreach ( $result as $x ) {
    echo '<tr>';
    foreach ( $x as $y ) {
        echo '<td>' . $y . '</td>';
    }
    echo '</tr>';
    }
    echo '</tbody>';
}