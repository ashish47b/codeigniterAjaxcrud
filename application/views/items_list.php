<!-- items_list.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="./assets/styles.css">

</head>

<body>
    <h1>Items List</h1>

    <!-- Display a form to create a new item -->
    <form id="createItemForm">
        <div class="mb-3 createItemDiv" id="createItemDiv">
            <div class="form-group" id="create_0">
                <label for="Name" class="form-label">Name:*</label>
                <input type="text" class="name" name="name[]" required><br>
                <label for="description">Description:*</label>
                <textarea class="description" name="description[]" required></textarea><br>
                <label for="price">Price:*</label>
                <input type="number" class="price" name="price[]" step="0.01" required><br>
                <label for="images">Images:</label>
                <input type="file" class="images" name="images[]" multiple><br>
            </div>
        </div>
        <button type="button" class="btn btn-primary btn-xs mb-3" id="addMore">Add More</button>
        <button type="button" class="btn btn-success btn-xs mb-3 addproduct">Create Item</button>
        <button type="button" class="btn btn-primary btn-xs mb-3 backto" style=" display: none;">Back </button>
    </form>

    <!-- Display items -->
    <h1>Items List</h1>
    <table id="itemsTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Table rows will be populated dynamically using AJAX -->
        </tbody>
    </table>
    <!-- Include any necessary JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    // AJAX request to create a new item

    $('#addMore').click(function() {
        var createDiv = $('#createItemDiv');
        var cloneDiv = createDiv.children(':first').clone(); // Clone the first div inside createItemDiv

        // Clear input values of the cloned elements
        cloneDiv.find('input').val('');
        cloneDiv.find('select').val('');
        cloneDiv.find('textarea').val('');

        var id = 'create_' + createDiv.children().length; // Generate a unique id for the clone
        // Update the id attribute of the clone
        cloneDiv.attr('id', id);
        // Append the clone to the createItemDiv
        cloneDiv.appendTo(createDiv);
        validate();
    });
    $(document).on('click', '.addproduct', function(event) {
        event.preventDefault();
        // Flag for validation
        var validation = true;

        // Loop through input fields with name 'name[]'
        $("input[name='name[]'], input[name='price[]'], input[name='description[]']").each(function() {
            // Check if input value is empty
            if ($(this).val() === '') {
                validation = false;
                return false; // Exit the loop early if any field is empty
            }
        });

        // If validation fails, display alert and return
        if (!validation) {
            alert('Please fill in all the required fields.');
            return;
        }
        var formData = new FormData($('#createItemForm')[0]);
        // AJAX request
        $.ajax({
            url: 'ItemsController/createItem', // Adjust the URL as per your server setup
            type: 'post',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status == 200) {
                    alert(response.massege);
                    location.reload()
                } else {
                    alert(response.massege);
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred while processing your request. Please try again.');
                console.log(xhr.responseText);
                location.reload()
            }
        });
    });

    $(document).ready(function() {
        // Fetch data from the server using AJAX
        $.ajax({
            url: 'ItemsController/getItems',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Populate the table with fetched data
                $.each(response, function(index, item) {
                    var newRow = '<tr>' +
                        '<td>' + item.id + '</td>' +
                        '<td>' + item.name + '</td>' +
                        '<td>' + item.price + '</td>' +
                        '<td class="action-buttons">';

                    // Check if final_submit is 0
                    if (item.final_submit == 0) {
                        newRow +=
                            '<button class="btn sm btn-info btn-xs mb-3 edit" data-action="view" data-id="' +
                            item.id +
                            '">View</button>' +
                            '<button class="btn sm btn-primary btn-xs mb-3 edit" data-action="edit" data-id="' +
                            item.id +
                            '">Edit</button>' +
                            '<button class="btn sm btn-danger btn-xs mb-3 delete" data-id="' +
                            item.id +
                            '">Delete</button>' +
                            '<button class="btn sm btn-warning btn-xs mb-3 finalSubmit" data-finalSubmit="' +
                            item.final_submit +
                            '" data-id="' +
                            item.id +
                            '">Final Submit</button>';
                    } else {
                        newRow +=
                            '<button class="btn sm btn-info btn-xs mb-3 edit" data-action="view" data-id="' +
                            item.id +
                            '">View</button>';
                    }

                    newRow += '</td>' +
                        '</tr>';
                    $('#itemsTable tbody').append(newRow);
                });
            },
            error: function(xhr, status, error) {
                alert('An error occurred while fetching data. Please try again.');
                console.log(xhr.responseText);
            }
        });
    });
    // Edit button click handler
    $(document).on('click', '.edit', function() {
        var itemId = $(this).data('id');
        var action = $(this).data('action');
        // Perform edit action, e.g., redirect to edit page with itemId
        $.ajax({
            url: 'ItemsController/singleItem',
            type: 'POST',
            data: {
                id: itemId
            },
            dataType: 'json',
            success: function(response) {
                // Check if response is not empty
                if (response.length > 0) {
                    var item = response[0]; // Take the first item from the array
                    // Construct HTML for the item
                    if (action == 'view') {
                        var readonly = 'readonly';
                        var displayNone = 'style="display: none;"';
                        $('#addMore').css("display", "none");
                        $('.addproduct').css("display", "none");
                        $('.backto').css("display", "block");
                    } else {
                        var readonly = '';
                        var displayNone = 'style="display: block;"';
                        $('#addMore').css("display", "");
                        $('.addproduct').css("display", "");
                        $('.backto').css("display", "none");
                    }
                    var nameArray = JSON.parse(item.name);
                    var priceArray = JSON.parse(item.price);
                    var descriptionArray = JSON.parse(item.description);
                    var imageUrlArray = JSON.parse(item.image_url);

                    var newItemHTML =
                        ''; // Initialize an empty string to accumulate HTML for all items
                    newItemHTML +=
                        '<input type="hidden" class="name" value="' + item
                        .id +
                        '" name="id" required><br>';
                    nameArray.forEach(function(itemName, key) {

                        newItemHTML +=
                            '<div class="form-group" id="create_' + key +
                            '">'; // Open a container for each item
                        newItemHTML += '<label for="name">Name:</label>';
                        newItemHTML +=
                            '<input type="text" class="name" value="' +
                            itemName +
                            '" name="name[]" ' + readonly + ' required><br>';
                        newItemHTML +=
                            '<label for="description">Description:</label>';
                        newItemHTML +=
                            '<textarea class="description"  ' + readonly +
                            ' name="description[]" required>' +
                            descriptionArray[key] + '</textarea><br>';
                        newItemHTML += '<label for="price">Price:</label>';
                        newItemHTML +=
                            '<input type="number" ' + readonly +
                            ' class="price" name="price[]" step="0.01" value="' +
                            priceArray[key] + '" required><br>';
                        newItemHTML +=
                            '<label for="image_url">Image URL:</label>';
                        newItemHTML +=
                            '<input type="file" ' + displayNone + ' ' +
                            readonly +
                            ' class="image_url" name="images[]" value="">';
                        newItemHTML +=
                            '<img src="./assets/uploads/' +
                            imageUrlArray[key] +
                            '" alt="' +
                            itemName +
                            '" width="100" height="100" /><br>';
                        newItemHTML +=
                            '<input type="hidden" ' + readonly +
                            ' class="image_url" name="imagesOld[]" value="' +
                            imageUrlArray[key] + '"><br>';
                        newItemHTML +=
                            '</div>'; // Close the container for each item
                    });

                    // Empty and append HTML to the container after the loop
                    $('#createItemDiv').empty().append(newItemHTML);

                } else {
                    // Handle empty response
                    alert('Empty response received.');
                }
            },
            error: function(xhr, status, error) {
                alert(
                    'An error occurred while processing your request. Please try again.'
                );
                console.log(xhr.responseText);
            }
        });
    });
    $(document).on('click', '.delete', function() {
        var itemId = $(this).data('id');
        var confirmDelete = confirm('Are you sure you want to delete this item?');
        if (confirmDelete) {
            // Perform delete action using AJAX
            $.ajax({
                url: 'ItemsController/deleteItem',
                type: 'POST',
                data: {
                    id: itemId
                },
                success: function(response) {
                    location.reload(); // Refresh the page
                },
                error: function(xhr, status, error) {
                    alert(
                        'An error occurred while processing your request. Please try again.'
                    );
                    console.log(xhr.responseText);
                }
            });
        }
    });
    $(document).on('click', '.finalSubmit', function() {
        var itemId = $(this).data('id');
        var final_submit = $(this).data('finalsubmit');

        if (final_submit == '0') {
            var confirmDelete = confirm('Are you sure you want to Final Submit this item?');
            if (confirmDelete) {
                $.ajax({
                    url: 'ItemsController/finalSubmit',
                    type: 'POST',
                    data: {
                        id: itemId
                    },
                    success: function(response) {
                        location.reload(); // Refresh the page
                    },
                    error: function(xhr, status, error) {
                        alert(
                            'An error occurred while processing your request. Please try again.'
                        );
                        console.log(xhr.responseText);
                    }
                });
            } else {
                location.reload(); // Refresh the page
            }
        } else {
            alert(
                'This product Alerdy Final Submit.'
            );
        }
    });
    $(document).on('click', '.backto', function() {

        location.reload(); // Refresh the page

    });

    function validate() {
        $('#createItemForm').validate({
            rules: {
                // Define validation rules for your form fields here
                // For example:
                name: {
                    required: true
                },
                price: {
                    required: true,
                    number: true
                }
            },
            messages: {
                // Define custom error messages for each rule here
                // For example:
                name: {
                    required: "Please enter a name."
                },
                price: {
                    required: "Please enter a price.",
                    number: "Please enter a valid number."
                }
            }
        });
    }
    </script>
</body>

</html>