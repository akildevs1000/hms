<div class="header">
    <table class="">
        <tr>
            <td class="text-left border-none col-4">
                <div class="logo pt">
                    @if (env('APP_ENV') == 'production')
                        <img src="{{ urldecode($logo) }}" height="100px" width="100"
                            style="margin-left: 50px;margin-top: 0px">
                    @else
                        <img src="https://backend.ezhms.com/upload/app-logo.jpg" alt="Hotel Logo" class="logo" />
                    @endif

                </div>
            </td>
            <td class="text-center border-none col-4 uppercase"></td>
            <td class="text-right border-none col-4">
                <h5 class="reds">{{ $name }}</h5>
                <div class="greens" style="line-height: 1">
                    <small>{{ $location }}</small>
                </div>
                <div class="greens" style="line-height: 1">
                    <small>{{ $user['email'] }}</small>
                </div>
            </td>
        </tr>
    </table>
</div>
