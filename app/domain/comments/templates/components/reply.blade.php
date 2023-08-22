<div id="#{{ $comment['id'] }}">
    <div class="commentImage">
        <x-users::profile-image :userId="$comment['userId']" />
    </div>
    <div class="commentMain">
        <div class="commentContent">
            @fragment('content')
                {!! sprintf(
                    __('text.written_on'),
                    $tpl->getFormattedDateString($comment['date']),
                    $tpl->getFormattedTimeString($comment['date'])
                ) !!}
            @endfragment
        </div>

        <span class="name">{!! sprintf(
            __('text.full_name'),
            $tpl->escape($comment['firstname']),
            $tpl->escape($comment['lastname'])
        ) !!}</span>

        <div class="text">
            {!! $tpl->escapeMinimal($comment['text']) !!}
        </div>

        @if($isthecurrentuser)
            <button
                hx-post="/hx/comment/delete-comment/{{ $comment['id'] }}"
                hx-swap="innerHTML"
                hx-target="#{{ $comment['id'] }} .commentContent"
            >Update Comment</button>
        @endif
    </div>
    <div class="clearall"></div>
</div>
