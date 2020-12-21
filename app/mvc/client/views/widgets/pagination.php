<?php
if (array_key_exists("sort", $_GET)) {

    if ($page != 1) {
        $pstr_prev = '<span><a class="pstr_prev" href="' . $main_page . 'sort=' . $sorting . '&page=' . ($page - 1) . '">&#171; Предыдущая</a></span>';
    }
    if ($page != $total) {
        $pstr_next = '<span><a class="pstr_next" href="' . $main_page . 'sort=' . $sorting . '&page=' . ($page - 1) . '">Следующая &#187;</a></span>';
    }

    //if($page - 5 > 0) $page5left = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page - 5).'&sort='.$sorting.'">'.($page-5).'</a></span>';
    //if($page - 4 > 0) $page4left = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page - 4).'&sort='.$sorting.'">'.($page-4).'</a></span>';
    //if($page - 3 > 0) $page3left = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page - 3).'&sort='.$sorting.'">'.($page-3).'</a></span>';
    if ($page - 2 > 0)
        $page2left = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page - 2) . '&sort=' . $sorting . '">' . ($page - 2) . '</a></span>';
    if ($page - 1 > 0)
        $page1left = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page - 1) . '&sort=' . $sorting . '">' . ($page - 1) . '</a></span>';

    //if($page + 5 <= $total) $page5right = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page + 5).'&sort='.$sorting.'">'.($page+5).'</a></span>';
    //if($page + 4 <= $total) $page4right = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page + 4).'&sort='.$sorting.'">'.($page+4).'</a></span>';
    //if($page + 3 <= $total) $page3right = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page + 3).'&sort='.$sorting.'">'.($page+3).'</a></span>';
    if ($page + 2 <= $total)
        $page2right = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page + 2) . '&sort=' . $sorting . '">' . ($page + 2) . '</a></span>';
    if ($page + 1 <= $total)
        $page1right = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page + 1) . '&sort=' . $sorting . '">' . ($page + 1) . '</a></span>';

    if ($page + 3 < $total) {
        $strtotal = '<span class="nav-point">...</span><span><a href="' . $main_page . 'sort=' . $sorting . '&page=' . $total . '">' . $total . '</a></span>';
    } else {
        $strtotal = "";
    }

    if ($total > 1) {
        echo '
            <ul>';
        echo $pstr_prev . $page2left . $page1left . '<span><a class="page current" href="' . $main_page . '&sort=' . $sorting . '&page=' . $page . '">' . $page . '</a></span>' . $page1right . $page2right . $strtotal . $pstr_next;
        echo '
            </ul>';
    }
} else {
    if ($page != 1) {
        $pstr_prev = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page - 1) . '">&#171; Предыдущая</a></span>';
    } else {
        $pstr_prev = '';
    }
    if ($page != $total) {
        $pstr_next = '<span><a class="pstr_next" href="' . $main_page . 'page=' . ($page + 1) . '">Следующая &#187;</a></span>';
    } else {
        $pstr_next = '';
    }

    //if($page - 5 > 0) $page5left = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page - 5).'">'.($page-5).'</a></span>';
    //if($page - 4 > 0) $page4left = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page - 4).'">'.($page-4).'</a></span>';
    //if($page - 3 > 0) $page3left = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page - 3).'">'.($page-3).'</a></span>';
    if ($page - 2 > 0) {
        $page2left = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page - 2) . '">' . ($page - 2) . '</a></span>';
    } else {
        $page2left = '';
    }
    if ($page - 1 > 0) {
        $page1left = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page - 1) . '">' . ($page - 1) . '</a></span>';
    } else {
        $page1left = '';
    }

    //if($page + 5 <= $total) $page5right = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page + 5).'">'.($page+5).'</a></span>';
    //if($page + 4 <= $total) $page4right = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page + 4).'">'.($page+4).'</a></span>';
    //if($page + 3 <= $total) $page3right = '<span><a class="pstr_prev" href="'.$main_page.'page='.($page + 3).'">'.($page+3).'</a></span>';
    if ($page + 2 <= $total) {
        $page2right = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page + 2) . '">' . ($page + 2) . '</a></span>';
    } else {
        $page2right = '';
    }
    if ($page + 1 <= $total) {
        $page1right = '<span><a class="pstr_prev" href="' . $main_page . 'page=' . ($page + 1) . '">' . ($page + 1) . '</a></span>';
    } else {
        $page1right = '';
    }

    if ($page + 3 < $total) {
        $strtotal = '<span class="nav-point">...</span><span><a href="' . $main_page . 'page=' . $total . '">' . $total . '</a></span>';
    } else {
        $strtotal = "";
    }

    if ($total > 1) {
        echo $pstr_prev . $page2left . $page1left . '<span><a class="page current" href="' . $main_page . '&sort=' . $sorting . '&page=' . $page . '">' . $page . '</a></span>' . $page1right . $page2right . $strtotal . $pstr_next;
    }
}