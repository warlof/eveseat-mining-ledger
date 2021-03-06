@extends('web::character.layouts.view', ['viewname' => 'mining-ledger'])

@section('title', trans_choice('web::seat.character', 1) . ' ' . trans('mining-ledger::seat.mining'))
@section('page_header', trans_choice('web::seat.character', 1) . ' ' . trans('mining-ledger::seat.mining'))

@inject('request', Illuminate\Http\Request')

@section('character_content')
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
            <h3 class="panel-title pull-left">{{ trans('mining-ledger::seat.mining') }}</h3>
            <div class="pull-right">
                @if(is_null($token))
                <a href="{{ route('auth.mining_ledger', ['character', request()->route()->parameter('character_id')]) }}">Activate</a>
                @else
                <span class="label label-success">Enabled</span>
                @endif
            </div>
        </div>
        <div class="panel-body">
            <table class="table compact table-condensed table-hover table-responsive" id="character-mining-ledger">
                <thead>
                    <tr>
                        <th>{{ trans('web::seat.date') }}</th>
                        <th>{{ trans('web::seat.system') }}</th>
                        <th>{{ trans('mining-ledger::seat.ore') }}</th>
                        <th>{{ trans('web::seat.quantity') }}</th>
                        <th>{{ trans('web::seat.volume') }}</th>
                        <th>{{ trans('web::seat.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ledger as $entry)
                    <tr data-character-id="{{ request()->character_id }}" data-date="{{ $entry->date }}"
                        data-system-id="{{ $entry->system->itemID }}" data-system-name="{{ $entry->system->itemName }}"
                        data-type-id="{{ $entry->type_id }}" data-type-name="{{ $entry->type->typeName }}">
                        <td data-order="{{ $entry->date }}">
                            <span data-toggle="tooltip" data-placement="top" title="{{ $entry->date }}">{{ $entry->date }}</span>
                        </td>
                        <td data-order="{{ $entry->system->itemName }}">
                            <a href="//evemaps.dotlan.net/system/{{ $entry->system->itemName }}" target="_blank">
                                <span class="fa fa-map-marker"></span>
                            </a>
                            {{ $entry->system->itemName }}
                        </td>
                        <td data-order="{{ $entry->type->typeName }}">
                            {!! img('type', $entry->type->typeID, 32, ['class' => 'img-circle eve-icon small-icon']) !!}
                            {{ $entry->type->typeName }}
                        </td>
                        <td class="text-right" data-order="{{ $entry->quantity }}">{{ number_format($entry->quantity) }}</td>
                        <td class="text-right" data-order="{{ $entry->volumes }}">{{ number_format($entry->volumes, 2) }} m3</td>
                        <td class="text-right" data-order="{{ $entry->amount }}">
                            {{ number_format($entry->amount, 2) }} ISK
                            <a href="#" class="btn btn-sm btn-link" data-toggle="modal" data-target="#detailed-ledger">
                                <i class="fa fa-cubes"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @include('mining-ledger::character.includes.modal')
@stop

@push('javascript')
    <script type="text/javascript">
        $(function(){
            $('#character-mining-ledger').dataTable({
                'order': [
                    [0, 'desc'],
                    [3, 'desc']
                ]
            });

            $('#detailed-ledger')
                .on('show.bs.modal', function(e){
                    var row = $(e.relatedTarget).closest('tr');
                    var imgType = $('#detailed-ledger').find('h4.modal-title img').first();
                    var link = '{{ route('character.view.detailed_mining_ledger', [request()->character_id, carbon()->toDateString(), 0, 0]) }}';
                    var imgTypeRegex = /(\/\/image.eveonline.com\/Type\/)([0-9]+)(_32.png)/gi;
                    var ajaxRegex = /(https?:\/\/[a-z0-9\/.]+(:[0-9]+)?\/character\/view\/mining-ledger\/[0-9]+\/)([0-9]{4}-[0-9]{2}-[0-9]{2}\/0\/0)/gi;
                    var imgSrc = imgTypeRegex.exec(imgType.attr('src'));

                    imgType.attr('src', imgSrc[1] + row.attr('data-type-id') + imgSrc[3]);
                    link = ajaxRegex.exec(link)[1] + row.attr('data-date') + '/' + row.attr('data-system-id') + '/' + row.attr('data-type-id');

                    $('#modal-ledger-system-name').text(row.attr('data-system-name'));
                    $('#modal-ledger-date').text(row.attr('data-date'));
                    $('#modal-ledger-type-name').text(row.attr('data-type-name'));

                    var table = $('#hourly-ledger');

                    table.DataTable({
                        "ajax" : {
                            "url": link,
                            "dataSrc":""
                        },
                        "processing": true,
                        "columns": [
                            { "data": "time" },
                            { "data": "quantity"},
                            { "data": "volumes"},
                            { "data": "amount"}
                        ],
                        "columnDefs": [
                            {
                                "render": function(data, type, row) {
                                    return data.toLocaleString();
                                },
                                "targets": 1
                            },
                            {
                                "render": function(data, type, row) {
                                    return data.toLocaleString() + " m3";
                                },
                                "targets": 2
                            },
                            {
                                "render": function(data, type, row) {
                                    return data.toLocaleString() + " ISK";
                                },
                                "targets": 3
                            }
                        ],
                        "order": [
                            [0, 'asc'],
                            [3, 'desc']
                        ]
                    });

                })
                .on('hidden.bs.modal', function(e){
                    var table = $('#hourly-ledger').DataTable();
                    table.destroy();
                });
        });
    </script>
@endpush
