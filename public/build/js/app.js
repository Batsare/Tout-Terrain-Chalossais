$( "#messages_guestbook" ).click(function() {
    $("#form_guestbook").toggleClass('disabled enabled');
    $("#posts_guestbook").toggleClass('enabled disabled');

});

$( "#post_message_guestbook" ).click(function() {
    $("#posts_guestbook").toggleClass('disabled enabled');
    $("#form_guestbook").toggleClass('enabled disabled');
});