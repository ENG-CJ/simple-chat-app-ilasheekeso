<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />

  <title>white chat - Bootdey.com</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../style.css" />
</head>

<body>
  <main class="content">
    <div class="container p-0">
      <h5 class="h3 mb-3">My Chats</h5>
      <div class="card">
        <div class="row g-0">
          <!-- active usetrs -->
          <div class="col-12 col-lg-5 col-xl-3 border-right">
            <div class="p-2">
              <h6>All Users</h6>
            </div>

            <div class="list-users">


            </div>
            <!-- end -->
            <hr class="d-block d-lg-none mt-1 mb-0" />
          </div>
          <!--end-->

          <!-- messages -->

          <div class="col-12 col-lg-7 col-xl-9">
            <?php
            if (isset($_GET['id']) && isset($_GET['name'])) {

              // start
            ?>
              <input type="hidden" class="to_id" value="<?php echo $_GET['id'] ?>" />
              <div class="py-2 px-4 border-bottom d-none d-lg-block">
                <div class="d-flex align-items-center py-1">
                  <div class="position-relative">
                    <img src="../images/user.png" class="rounded-circle mr-1" alt="Sharon Lessman" width="40" height="40" />
                  </div>
                  <div class="flex-grow-1 pl-3">
                    <strong><?php echo $_GET['name'] ?></strong>
                    <div class="text-muted small"><em>Selected User</em></div>
                  </div>
                </div>
              </div>
              <div class="body-messages">

              </div>

              <div class="flex-grow-0 py-3 px-4 border-top">
                <div class="input-group">
                  <input type="text" class="form-control message" placeholder="Type your message" />
                  <button class="btn btn-primary ml-2 send">Send</button>
                </div>
              </div>
            <?php

              // end
            } else {
            ?>
              <h6 class='text-center'>No Selected User</h6>
              <p class='text-muted text-center'>1st, Select The User You want to interpret</p>
            <?php
            }

            ?>

          </div>
          <!-- end messages -->

        </div>
      </div>
      <a href="../login.html">Logout</a>
    </div>
  </main>
  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript"></script>

  <script src="jquery-3.3.1.min.js"></script>

  <script>
    $(document).ready(() => {
      const createChat = () => {
        var data = {
          to_user: $(".to_id").val(),
          message: $(".message").val(),
          action: "createChat",
        };
        $.ajax({
          method: "POST",
          url: "../api/users.php",
          dataType: "json",
          data: data,
          success: (res) => {
            // $(".chat-messages").scrollTop($(".chat-messages")[0].scrollHeight);
            console.log(res)
            $(".message").val("");
            // readMessages()

          },
          error: (err) => {
            $(".message").val("");
          },
        });
      }

      $('.message').on('keydown', function(event) {
        if (event.which === 13 && $(this).val().trim() !== "") {
          event.preventDefault();
          createChat();

        }
      });

      $(".send").click(() => {
        if ($(".message").val() == "")
          return;
        else
          createChat();
      });
      const readMessages = () => {
        if ($('.to_id').val() == "")
          return;
        var data = {
          toUser: $('.to_id').val(),
          "action": "fetchMessages"
        }
        var chatContainer = $('.body-messages')[0];
        var isAtBottom = chatContainer.scrollHeight - chatContainer.clientHeight <= chatContainer.scrollTop + 1;
        $.ajax({
          method: "POST",
          url: "../api/users.php",
          data: data,
          success: (res) => {
            var scrollPositionBefore = chatContainer.scrollHeight - chatContainer.scrollTop;
            $('.body-messages').html(res)
            if (isAtBottom) {
              chatContainer.scrollTop = chatContainer.scrollHeight;
            } else {
              chatContainer.scrollTop = chatContainer.scrollHeight - scrollPositionBefore;
            }
            console.log(res)
          },
          error: (err) => {
            console.log(err)
          },
        })

      }

      // readMessages()
      setInterval(readMessages, 80);
      const readUsers = () => {
        var data = {
          action: "readUsers",
        };
        $.ajax({
          method: "POST",
          dataType: "json",
          url: "../api/users.php",
          data: data,
          success: (res) => {
            const {
              found,
              status,
              message,
              data
            } = res;
            if (found) {
              data.forEach(value => {
                $(".list-users").append(`
                   <a
                href="./index.php?id=${value.id}&name=${value.username}&image=${value.profile}"
                class="list-group-item list-group-item-action border-0"
              >
                <div class="d-flex align-items-start">
                  <img
                    src=${value.profile=="no_profile" ?"../images/user.png" : "../images/"+value.profile}
                    class="rounded-circle mr-1"
                    alt="Sharon Lessman"
                    width="40"
                    height="40"
                  />
                  <div class="flex-grow-1 ml-3">
                    ${value.username}
                    <div class="small">
                      <span class="fas fa-circle chat-online"></span> Active
                      User
                    </div>
                  </div>
                </div>
              </a>

                  
                  `)
              })
            } else {

            }
          },
          error: (err) => {},
        });
      };
      readUsers()
    });
  </script>
</body>

</html>