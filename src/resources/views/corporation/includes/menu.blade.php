<ul class="nav nav-pills">
    <li role="presentation" @if($sub_viewname == 'ledger')class="active"@endif>
        <a href="{{ route('corporation.view.mining_ledger', request()->route()->parameter('corporation_id')) }}">Ledger</a>
    </li>
    <li role="presentation" @if($sub_viewname == 'tracking')class="active"@endif>
        <a href="{{ route('corporation.view.mining_tracking', request()->route()->parameter('corporation_id')) }}">Tracking</a>
    </li>
</ul>
