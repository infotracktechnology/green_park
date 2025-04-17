@extends('layouts.dashboard')

@section('title', 'OMR Sheet')

@section('main')
<style>
    .omr-wrapper {
        max-width: 780px;
        margin: 0 auto;
        overflow-x: auto;
        padding: 10px;
    }

    .omr-table {
        width: 100%;
        min-width: 750px;
        border-collapse: collapse;
        font-size: 12px;
        table-layout: fixed;
    }

    .omr-table th,
    .omr-table td {
        border: 1px solid #ddd;
        text-align: center;
        padding: 4px 0px;
    }

    .omr-table th {
        background-color: #f8f8f8;
        font-weight: 600;
    }

    .bubble {
        width: 14px;
        height: 14px;
        border: 1px solid #888;
        border-radius: 50%;
        display: inline-block;
        position: relative;
    }

    .bubble input {
        opacity: 0;
        position: absolute;
        width: 100%;
        height: 100%;
        margin: 0;
        cursor: pointer;
    }

    .bubble input:checked ~ .custom-bubble {
        background-color: #000;
        box-shadow: inset 0 0 1.5px #333;
    }

    .custom-bubble {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        display: inline-block;
    }

    .red-text {
        color: red;
        font-weight: bold;
        font-size: 12px;
    }

    .green-text {
        color: green;
        font-weight: bold;
        font-size: 12px;
    }

    .submit-container {
        text-align: center;
        margin-top: 20px;
    }
</style>

<div class="main-content">
    <div class="section-header text-center">
        <h5 class="mb-3 text-primary">Mock Test (OMR Sheet)</h5>

        <div class="form-group d-inline-block mb-3">
            <label for="testSelect" class="mr-2">Select Test Name</label>
            <select name="test" id="testSelect" class="form-control form-control-sm d-inline-block" style="min-width: 120px;">
                <option value="">Select Test Name</option>
                <option value="A">A</option>
                <option value="B">B</option>
            </select>
        </div>
    </div>
    
    <div class="section-body">
        <form action="#" method="POST">
            @csrf

            <div class="omr-wrapper">
                <table class="omr-table">
                    <thead>
                        <tr>
                            @for ($col = 0; $col < 5; $col++)
                                <th colspan="5" class="red-text">Response</th>
                            @endfor
                        </tr>
                        <tr>
                            @for ($col = 0; $col < 5; $col++)
                                <th class="red-text">Q.No</th>
                                <th class="red-text">1</th>
                                <th class="red-text">2</th>
                                <th class="red-text">3</th>
                                <th class="red-text">4</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @for ($row = 0; $row < 36; $row++)
                            <tr>
                                @for ($col = 0; $col < 5; $col++)
                                    @php
                                        $q = $row + 1 + ($col * 36);
                                    @endphp
                                    @if ($q <= 180)
                                        <td><span class="green-text">{{ $q }}</span></td>
                                        @for ($opt = 1; $opt <= 4; $opt++)
                                            <td>
                                                <label class="bubble">
                                                    <input type="radio" name="answers[{{ $q }}]" value="{{ $opt }}" @if(isset($existingAnswers[$q]) && $existingAnswers[$q] == $opt) checked @endif>
                                                    <span class="custom-bubble"></span>
                                                </label>
                                            </td>
                                        @endfor
                                    @endif
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <div class="submit-container">
                <button type="submit" class="btn btn-success px-4 py-2">Final Submit</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')

<script>
document.addEventListener('DOMContentLoaded', function () {
    const radios = document.querySelectorAll('input[type="radio"]');

    radios.forEach(input => {
        input.addEventListener('change', function () {
            document.querySelectorAll(`input[name="${this.name}"]`).forEach(i => {
                i.removeAttribute('checked');
            });
            this.setAttribute('checked', 'checked');
        });
    });
});
</script>


@endsection