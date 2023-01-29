(function ($) {
  "use strict";

  /*==================================================================
    [ Validate ]*/
  var input = $(".validate-input .input100");
  try {
    const stored = localStorage.getItem("lgdi");
    let loggedIn = false;
    if (stored) {
      const storedObj = JSON.parse(stored);
      if (
        storedObj?.email == "admin@switches.net" &&
        storedObj?.password == "123switch"
      ) {
        loggedIn = true;
      }
    }
    if (loggedIn) {
      $(location).attr("href", "http://localhost/autoturn/switch.php");
    }
  } catch (err) {
    alert(`Error: ${err.message}`);
  }

  $(".validate-form").on("submit", function () {
    var check = true;

    for (var i = 0; i < input.length; i++) {
      if (validate(input[i]) == false) {
        showValidate(input[i]);
        check = false;
      }
    }

    const email = $("#email-field").val();
    const password = $("#pass-field").val();
    try {
      if (check) {
        if (email != "admin@switches.net" || password != "123switch") {
          alert("Invalid email or password");
          return false;
        } else {
          localStorage.setItem("lgdi", JSON.stringify({ email, password }));
          $(location).attr("href", "http://localhost/autoturn/switch.php");
        }
      }
    } catch (e) {
      alert(e.message);
    }

    return false;
  });

  $(".validate-form .input100").each(function () {
    $(this).focus(function () {
      hideValidate(this);
    });
  });

  function validate(input) {
    if ($(input).attr("type") == "email" || $(input).attr("name") == "email") {
      if (
        $(input)
          .val()
          .trim()
          .match(
            /^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/
          ) == null
      ) {
        return false;
      }
    } else {
      if ($(input).val().trim() == "") {
        return false;
      }
    }
  }

  function showValidate(input) {
    var thisAlert = $(input).parent();

    $(thisAlert).addClass("alert-validate");
  }

  function hideValidate(input) {
    var thisAlert = $(input).parent();

    $(thisAlert).removeClass("alert-validate");
  }
})(jQuery);
