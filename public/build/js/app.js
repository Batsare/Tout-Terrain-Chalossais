postGuestbook = $("#posts_guestbook");
formGuestbook = $("#form_guestbook");

$( "#messages_guestbook" ).click(function() {
    if( postGuestbook.hasClass('enabled')){
    }else{
        formGuestbook.toggleClass('disabled enabled');
        postGuestbook.toggleClass('enabled disabled');
    }

});

$( "#post_message_guestbook" ).click(function() {
    if( formGuestbook.hasClass('enabled')){
    }else{
        postGuestbook.toggleClass('disabled enabled');
        formGuestbook.toggleClass('enabled disabled');
    }

});