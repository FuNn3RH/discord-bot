<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
    <title>Attendances</title>
</head>

<body>

    <div class="p-3">

        <div class="row">
            <div class="col-12">
                <h1 class="text-center"><a href="#payments" style="color: inherit;text-decoration: none">Attendances</a>
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <input type="radio" class="btn-check btn-filter" name="options-outlined" id="primary-outlined"
                            value="all" autocomplete="off" checked>
                        <label class="btn btn-outline-primary" for="primary-outlined">All</label>

                        <input type="radio" class="btn-check btn-filter" name="options-outlined" id="success-outlined"
                            value="1" autocomplete="off">
                        <label class="btn btn-outline-success" for="success-outlined">Paid</label>

                        <input type="radio" class="btn-check btn-filter" name="options-outlined" id="danger-outlined"
                            value="0" autocomplete="off">
                        <label class="btn btn-outline-danger" for="danger-outlined">Not Paid</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Date</th>
                            <th scope="col">Run ID</th>
                            <th scope="col">Advertiser</th>
                            <th scope="col">Count x Level</th>
                            <th scope="col">Dungeons</th>
                            <th scope="col">Pot</th>
                            <th scope="col">Cut</th>
                            <th scope="col">Boosters</th>
                            <th scope="col">Note</th>
                            <th scope="col">Paid</th>
                            <th scope="col">User</th>
                            <th scope="col">Message Link</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($runs as $date => $runData)
                            @foreach ($runData as $run)
                                @php
                                    $isFirst = false;
                                    if ($loop->first) {
                                        $isFirst = true;
                                    }

                                    $boostersName = collect($run->boosters)->map(fn($booster) => ucfirst($booster));

                                    $boosters = implode('-', $boostersName->toArray());
                                @endphp
                                <tr class="table-row" data-paid="{{ $run->paid }}" data-date="{{ $date }}"
                                    data-id="{{ $run->id }}" data-unit="{{ $run->unit }}"
                                    data-boosters="{{ $boosters }}">
                                    {{-- @if ($isFirst)
                                        <td scope="row">{{ $date }}</td>
                                    @else
                                        <td scope="row"> </td>
                                    @endif --}}
                                    <td scope="row" title="{{ $run->created_at }}">{{ $date }}</td>
                                    <td>{{ $run->id }}</td>
                                    <td>{{ ucfirst($run->adv) }}</td>
                                    <td>{{ $run->count }}x{{ $run->level }}</td>
                                    <td>{{ $run->dungeons }}</td>
                                    <td>{{ $run->pot . ucfirst($run->unit) }}</td>
                                    <td>{{ $run->price . ucfirst($run->unit) }}</td>
                                    <td>{{ $boosters }}</td>
                                    <td>{{ $run->note }}</td>
                                    <td>
                                        @if ($run->paid)
                                            <span class="badge bg-success" title="{{ $run->pay_user }}">Paid</span>
                                        @else
                                            <span class="badge bg-danger">Not Paid</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($run->user->name) }}</td>
                                    <td><a href="{{ $run->dmessage_link }}" target="_blank">Message</a></td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row col-2" style="margin: 0 auto;">

            <h2 id="payments" class="text-center">Payments</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>State</th>
                        <th>T</th>
                        <th>K</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment => $unit)
                        <tr>
                            <td>{{ ucfirst($payment) }}</td>
                            <td>{{ $unit['T'] }}</td>
                            <td>{{ $unit['K'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <script src="{{ asset('assets/app.js') }}"></script>
</body>

</html>
