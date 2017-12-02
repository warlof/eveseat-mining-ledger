@extends('mining-ledger::corporation.layouts.view', ['sub_viewname' => 'ledger'])

@section('title', trans_choice('web::seat.corporation', 1) . ' | ' . trans('mining-ledger::seat.mining') . ' ' . trans_choice('mining-ledger::seat.ledger', 2))
@section('page_header', trans_choice('web::seat.corporation', 1) . ' ' . trans('mining-ledger::seat.mining') . ' ' . trans_choice('mining-ledger::seat.ledger', 2))

@section('mining_content')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ trans_choice('mining-ledger::seat.available_ledger', $ledgers->count()) }}</h3>
        </div>
        <div class="panel-body">
            @foreach($ledgers->chunk(12) as $chunk)
            <div class="row">
                @foreach ($chunk as $ledger)
                <div class="col-xs-1">
                    <span class="text-bold">
                        <a href="{{ route('coporation.view.mining_ledger', [
                            request()->route()->parameter('corporation_id'),
                            date('Y', strtotime($ledger->year. '-01-01')),
                            date('m', strtotime($ledger->year . '-' . $ledger->month . '-01'))
                        ]) }}">{{ date('M Y', strtotime($ledger->year . "-" . $ledger->month . "-01")) }}</a>
                    </span>
                </div>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{{ trans_choice('mining-ledger::seat.ledger', 1) }}</h3>
        </div>
        <div class="panel-body">
            <table class="table compact table-condensed table-hover table-responsive" id="corporation-mining-ledger">
                <thead>
                    <tr>
                        <th>{{ trans_choice('web::seat.name', 1) }}</th>
                        <th>{{ trans('web::seat.quantity') }}</th>
                        <th>{{ trans('web::seat.volume') }}</th>
                        <th>{{ trans('web::seat.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                    <tr>
                        <td>
                            {!! img('character', $entry->character_id, 32, ['class' => 'img-circle eve-icon small-icon']) !!}
                            {{ $entry->name }}
                        </td>
                        <td class="text-right">{{ number_format($entry->quantities, 2) }}</td>
                        <td class="text-right">{{ number_format($entry->volumes, 2) }} m3</td>
                        <td class="text-right">{{ number_format($entry->amounts, 2) }} ISK</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop
