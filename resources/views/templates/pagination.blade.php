<?php
// config
$paginationConfig = Config::get('pagination.pagination');

$pageRange = $paginationConfig['pageRange'] + 4;

$paginator->appends($paginationData);

$itemCountPerPage = isset($paginationData['per_page']) ? $paginationData['per_page']: $paginationConfig['itemCountPerPage'];

$resultsPerPageArray = $paginationConfig['resultsPerPageArray'];
?>

<hr style="margin-top: 0;" />

<div class="pagination-result clearfix">

    <?php
    $firstItemNumber  = $paginator->total() ? (($paginator->currentPage() - 1) * $itemCountPerPage) + 1 : 0;
    $lastItemNumber =  $itemCountPerPage * $paginator->currentPage();

    if($lastItemNumber > $paginator->total()) {
        $lastItemNumber = $paginator->total();
    }

    ?>

    <span style="padding-top: 2px;">Showing {{ $firstItemNumber . " - " . $lastItemNumber . " of ".  $paginator->total() }} entries</span>

    <div class="results-per-page-div">
        {!! Form::open(['method' => 'GET', 'url' => UrlHelper::getUrl($controller, $action)]) !!}

        @if(!empty($paginationData))
            @foreach($paginationData as $k => $v)
                @if( ($k != 'per_page') OR ($k != 'p') )
                    {!! Form::hidden($k, $v) !!}
                    {!! Form::hidden('p', 1) !!}
                @endif
            @endforeach
        @endif

        Show
        {!! Form::select('per_page', $resultsPerPageArray, $itemCountPerPage, ['class' => 'no-select2 form-control', "onchange='this.form.submit()'"]) !!}
        entries
        {!! Form::close() !!}
    </div>

</div>

<div id="pagination">

    <ul class="pagination">

        <!-- First page link -->
        <li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }} hidden-xs">
            <a href="{{ $paginator->url(1) }}">&laquo;</a>
        </li>

        <!-- Previous page link -->
        <li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : '' }} prev-li">
            <a href="{{ $paginator->previousPageUrl() }}">
                <div class="hidden-xs">&lsaquo;</div>
                <div class="hidden-lg hidden-md hidden-sm">Prev</div>
            </a>
        </li>

        <!-- Numbered page links -->
        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <?php
            $half_total_links = floor($pageRange / 2);
            $from = $paginator->currentPage() - $half_total_links;
            $to = $paginator->currentPage() + $half_total_links;
            if ($paginator->currentPage() < $half_total_links) {
                $to += $half_total_links - $paginator->currentPage();
            }
            if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
            }
            ?>
            @if ($from < $i && $i < $to)
                <li class="{{ ($paginator->currentPage() == $i) ? ' active' : '' }} hidden-xs">
                    <a href="{{ $paginator->url($i) }}">{{ $i }}</a>
                </li>
        @endif
    @endfor


    <!-- Next page link -->
        <li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}">
            <a href="{{ $paginator->nextPageUrl() }}">
                <div class="hidden-xs">&rsaquo;</div>
                <div class="hidden-lg hidden-md hidden-sm">Next</div>
            </a>
        </li>

        <!-- Last page link -->
        <li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }} hidden-xs">
            <a href="{{ $paginator->url($paginator->lastPage()) }}">&raquo;</a>
        </li>
    </ul>

</div>