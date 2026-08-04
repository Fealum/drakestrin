@if(trim($message->message) !== '')
    {!! $forumFormatter->render($message->message, $message->smilies) !!}
@endif
