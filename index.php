<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fetch Data with Pagination</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
nav ul li a {
    display: inline-block;
    padding: 10px;
    background: red;
    margin: 5px;
    border-radius: 10px;
    color: white;
}
nav ul li{
    display: inline-block;
    }

    </style>
</head>
<body class="p-4">

    <div class="max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-4 text-center">Find Mainland Activity</h1>
        <form id="fetchForm" class="space-y-4" method="get">
            <div>
                <label for="searchQuery" class="block text-sm font-medium text-gray-700">Search:</label>
                <input type="text" placeholder="Search With Anything" id="searchQuery" name="searchQuery" class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </form>
    </div>

    <div id="results" class="mt-8 max-w-lg mx-auto"></div>
    <div id="pagination" class="mt-4 flex justify-center"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var currentPage = 1;
            var pageSize = 5;
            var totalPages;
              // Call fetchData() on input event
    $('#searchQuery').on('input', function(){
        fetchData();
    });

    // Call fetchData() on change event
    $('#searchQuery').on('change', function(){
        fetchData();
    });
            fetchData();
            $('#fetchForm').submit(function(e) {
                e.preventDefault();
                currentPage = 1;
                fetchData();
            });

            $('#pagination').on('click', '.page-link', function() {
                var page = $(this).data('page');
                if (page !== currentPage) {
                    currentPage = page;
                    fetchData();
                }
            });

            function fetchData() {
                var searchQuery = $('#searchQuery').val();
                var offset = (currentPage - 1) * pageSize;

                $.ajax({
                    url: 'https://activities.crmsoftware.ae/search',
                    type: 'GET',
                    data: {
                        q: searchQuery,
                        type: 'commercial',
                        limit: pageSize,
                        offset: offset
                    },
                    success: function(response) {
                        totalPages = Math.ceil(response.length / pageSize);
                        displayResults(response);
                    },
                    error: function(xhr, status, error) {
                        $('#results').html('<p>Error occurred while fetching data.</p>');
                    }
                });
            }

            function displayResults(data) {
    var html = '';
    var searchQuery = $('#searchQuery').val().trim().toLowerCase();
    
    if (data.length > 0) {
        html += '<ul class="divide-y divide-gray-200">';
        for (var i = 0; i < pageSize && (currentPage - 1) * pageSize + i < data.length; i++) {
            var index = (currentPage - 1) * pageSize + i;
            var name = data[index].name;
            var description = data[index].description;

            // Highlight matching text
            var highlightedName = name.replace(new RegExp(searchQuery, 'gi'), match => `<span class="bg-yellow-200">${match}</span>`);
            var highlightedDescription = description.replace(new RegExp(searchQuery, 'gi'), match => `<span class="bg-yellow-200">${match}</span>`);

            html += '<li class="py-4">';
            html += '<p class="text-lg font-semibold">' + data[index].acode + " | " + highlightedName + '</p>';
            html += '<p class="text-gray-500">' + highlightedDescription + '</p>';
            html += `<button class="bg-blue-500 hover:bg-blue-400 text-white font-bold py-2 px-4 border-b-4 border-blue-700 hover:border-blue-500 rounded">Inquire Now</button>`;
            html += '</li>';
        }
        html += '</ul>';
        renderPagination();
    } else {
        html += '<p>No results found.</p>';
        $('#pagination').html('');
    }
    $('#results').html(html);
}


            function renderPagination() {
                var startPage = Math.max(currentPage - 2, 1);
                var endPage = Math.min(startPage + 4, totalPages);

                var paginationHtml = '<nav aria-label="Pagination"><ul class="pagination">';
                if (currentPage > 1) {
                    paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage - 1) + '">Previous</a></li>';
                }

                for (var i = startPage; i <= endPage; i++) {
                    paginationHtml += '<li class="page-item ' + (i === currentPage ? 'active' : '') + '">';
                    paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + i + '</a>';
                    paginationHtml += '</li>';
                }

                if (currentPage < totalPages) {
                    paginationHtml += '<li class="page-item"><a class="page-link" href="#" data-page="' + (currentPage + 1) + '">Next</a></li>';
                }
                
                paginationHtml += '</ul></nav>';
                $('#pagination').html(paginationHtml);
            }
        });
    </script>
</body>
</html>
