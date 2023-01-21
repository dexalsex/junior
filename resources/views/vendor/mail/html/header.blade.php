<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://1000logos.net/wp-content/uploads/2021/04/Playboy-logo.png" class="logo" alt="PlayBoy">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
