$(document).ready(function() {

  // Display tickets button click event
  $('#display-tickets-btn').on('click', function() {
    // Send an AJAX request to retrieve tickets data from the server
    $.ajax({
      url: 'get_tickets.php',
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        // Display the tickets data in the table
        $('#tickets-table').empty();
        $.each(response, function(index, ticket) {
          var row = '<tr>' +
                      '<td>' + ticket.id + '</td>' +
                      '<td>' + ticket.name + '</td>' +
                      '<td>' + ticket.email + '</td>' +
                      '<td>' + ticket.type + '</td>' +
                      '<td>' + ticket.subject + '</td>' +
                      '<td>' + ticket.message + '</td>' +
                      '<td><button class="btn btn-primary reply-ticket-btn" data-ticket-id="' + ticket.id + '">Reply</button></td>' +
                    '</tr>';
          $('#tickets-table').append(row);
        });
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log('Error: ' + textStatus + ' - ' + errorThrown);
      }
    });
  });

  // Reply to ticket button click event
  $('#tickets-table').on('click', '.reply-ticket-btn', function() {
    // Get the ticket ID from the button data attribute
    var ticketId = $(this).data('ticket-id');
    // Send an AJAX request to retrieve ticket data from the server
    $.ajax({
      url: 'get_ticket.php?id=' + ticketId,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        // Display the ticket data in the reply form
        $('#reply-ticket-form input[name="id"]').val(response.id);
        $('#reply-ticket-form input[name="name"]').val(response.name);
        $('#reply-ticket-form input[name="email"]').val(response.email);
        $('#reply-ticket-form input[name="subject"]').val('RE: ' + response.subject);
        $('#reply-ticket-form textarea[name="message"]').val('');
        $('#reply-ticket-modal').modal('show');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log('Error: ' + textStatus + ' - ' + errorThrown);
      }
    });
  });

  // Submit reply button click event
  $('#submit-reply-btn').on('click', function() {
    // Get the form data
    var formData = new FormData($('#reply-ticket-form')[0]);
    // Send an AJAX request to submit the form data to the server
    $.ajax({
      url: 'submit_reply.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      dataType: 'json',
      success: function(response) {
        // Display the success message
        $('#reply-ticket-modal').modal('hide');
        alert('Reply submitted successfully');
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log('Error: ' + textStatus + ' - ' + errorThrown);
      }
    });
  });

});
