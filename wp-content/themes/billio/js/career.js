jQuery(document).ready(function($){

  'use strict';

  if($('#career-form').length){
    $('#career-form').submit(function(e){

      e.preventDefault();
      var $form=$(this),fieldForm={
        fullname:$('[name="fullname"]',$form),
        emailAddress:$('[name="email_address"]',$form),
        notes:$('[name="note"]',$form),
        career_id:$('[name="career_id"]',$form),
        attachment:$('[name="file_cv"]',$form)
      },error=false,modalbox=$form.closest('.modal');


      for (var fieldname in fieldForm) {

          var fieldEval=fieldForm[fieldname];

        if(fieldEval.val()==''){
          fieldEval.closest('.form-group').addClass('has-error');
          error=true;
        }else{
          fieldEval.closest('.form-group').removeClass('.has-error').addClass('has-success');
        }
      }

      if(error)
        return false;

      /* proccess form */
        var data = new FormData();     

        data.append( 'action', 'billio_apply_career' );
        data.append( 'fullname', fieldForm.fullname.val());
        data.append( 'email', fieldForm.emailAddress.val());
        data.append( 'notes', fieldForm.notes.val());
        data.append( 'career_id', fieldForm.career_id.val());

        $.each(fieldForm.attachment[0].files, function(i, file) {
            data.append('attachment', file);
        });

        var preloader=$('<div class="modal_preloader"><div class="modal_spinner-container"><div class="modal_loader"></div></div></div>');

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){

              if(modalbox.has(preloader).length){
                preloader.show();
              }else{
                modalbox.prepend(preloader);
              }
            },
            success: function(html){

              preloader.hide();
              var json = $.parseJSON(html);
              if(json.error){
                alert(json.error);
              }
              else{
                if(json.success){
                  modalbox.find('.md-description').html(json.success);
                }
                else{
                  modalbox.find('.md-close').click();
                }
              }

              $('#apply-career').remove();

            },
            error: function(){
            }
        });
      return false;
    });

  }

    if($('#career-send-form').length){
    $('#career-send-form').submit(function(e){

      e.preventDefault();
      var $form=$(this),fieldForm={
        fullname:$('[name="fullname"]',$form),
        emailAddress:$('[name="email_address"]',$form),
        notes:$('[name="note"]',$form),
        career_id:$('[name="career_id"]',$form),
        friendemailAddress:$('[name="friend_email_address"]',$form)
      },error=false,modalbox=$form.closest('.modal');


      for (var fieldname in fieldForm) {

          var fieldEval=fieldForm[fieldname];

        if(fieldEval.val()==''){
          fieldEval.closest('.form-group').addClass('has-error');
          error=true;
        }else{
          fieldEval.closest('.form-group').removeClass('.has-error').addClass('has-success');
        }
      }

      if(error)
        return false;

      /* proccess form */
        var data = new FormData();     

        data.append( 'action', 'billio_send_friend_career' );
        data.append( 'fullname', fieldForm.fullname.val());
        data.append( 'email', fieldForm.emailAddress.val());
        data.append( 'email_to', fieldForm.friendemailAddress.val());
        data.append( 'notes', fieldForm.notes.val());
        data.append( 'career_id', fieldForm.career_id.val());

        var preloader=$('<div class="modal_preloader"><div class="modal_spinner-container"><div class="modal_loader"></div></div></div>');

        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: data,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){

              if(modalbox.has(preloader).length){
                preloader.show();
              }else{
                modalbox.prepend(preloader);
              }
            },
            success: function(html){
              preloader.hide();
              var json = $.parseJSON(html);
              if(json.error){
                alert(json.error);
              }
              else{
                if(json.success){
                  modalbox.find('.md-description').html(json.success);
                }
                else{
                  modalbox.find('.md-close').click();
                }
              }
              $('#send-career-to-friend').remove();
            },
            error: function(){
            }
        });
      return false;
    });

  }
});