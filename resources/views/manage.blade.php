<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zeus Event Management - Admin Blog Management</title>
    <link rel="icon" href="{{ asset('img/Zeus808Logo.png') }}" type="image/png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-900 text-white">

<!-- Admin Panel Header -->
<div class="w-full bg-gray-800 p-4 md:p-6 flex flex-col md:flex-row justify-between items-center">
    <h1 class="text-2xl md:text-3xl font-semibold">Admin Blog Management</h1>
    <form action="{{ route('logout') }}" method="POST" class="mt-2 md:mt-0">
        @csrf
        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-500 transition">
            Logout
        </button>
    </form>
</div>

<!-- Admin Blog Management Layout -->
<div class="container mx-auto p-4 md:p-6 flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-6">
    <!-- Left Sidebar for Create/Edit Blog -->
    <div class="w-full md:w-1/3 bg-gray-800 p-6 rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-300 mb-4" id="modalTitle">Create/Edit Blog</h2>
        <form id="blogForm">
            @csrf
            <div class="mb-4">
                <label for="blogTitle" class="block text-gray-400">Title</label>
                <input type="text" id="blogTitle" name="title" class="w-full px-4 py-2 rounded bg-gray-700 text-white">
            </div>
            <div class="mb-4">
                <label for="blogDescription" class="block text-gray-400">Description</label>
                <textarea id="blogDescription" name="description" class="w-full px-4 py-2 rounded bg-gray-700 text-white"></textarea>
            </div>
            <div class="mb-4">
                <label for="blogType" class="block text-gray-400">Type</label>
                <select id="blogType" name="type" class="w-full px-4 py-2 rounded bg-gray-700 text-white">
                    <option value="1">Planning</option>
                    <option value="2">Moments</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="blogLink" class="block text-gray-400">Link</label>
                <input type="url" id="blogLink" name="link" class="w-full px-4 py-2 rounded bg-gray-700 text-white">
            </div>
            <button type="submit" class="w-full bg-yellow-500 text-gray-900 py-2 rounded font-bold hover:bg-yellow-400 transition">Save Blog</button>
        </form>
    </div>

    <!-- Right Section for Blog Management Table -->
    <div class="w-full md:flex-1 bg-gray-800 p-6 rounded-lg overflow-x-auto">
        <table class="w-full table-auto text-left">
            <thead>
            <tr class="text-gray-400 text-sm md:text-base">
                <th class="px-4 md:px-6 py-3">Title</th>
                <th class="px-4 md:px-6 py-3">Type</th>
                <th class="px-4 md:px-6 py-3">Actions</th>
            </tr>
            </thead>
            <tbody id="blogTableBody">
            <!-- Table rows will be populated dynamically -->
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    let editingBlogId = null;
    $(document).ready(function () {
        loadBlogs();
    });

    function loadBlogs() {
        fetchBlogs("");
    }

    function editBlog(blogId) {
        editingBlogId = blogId;
        fetch('{{ route('admin.blogs.show', '') }}/' + blogId)
            .then(response => response.json())
            .then(blog => {
                $("#modalTitle").text("Edit Blog");
                $("#blogTitle").val(blog.title);
                $("#blogDescription").val(blog.description);
                $("#blogType").val(blog.type);
                $("#blogLink").val(blog.link);
            });
    }

    function fetchBlogs(search = "") {
        const loader = `<tr><td colspan="3" class="text-center text-gray-500 p-4">Loading...</td></tr>`;
        $("#blogTableBody").html(loader);

        fetch('{{ route('admin.blogs.index') }}?q_=' + search)
            .then(response => response.json())
            .then(blogs => {
                let rows = '';
                blogs.forEach(blog => {
                    rows += `
                        <tr>
                            <td class="px-4 md:px-6 py-3 text-gray-300">${blog.title}</td>
                            <td class="px-4 md:px-6 py-3 text-gray-300">${blog.type === 1 ? 'Planning' : 'Moments'}</td>
                            <td class="px-4 md:px-6 py-3">
                                <button class="bg-blue-600 text-white px-3 md:px-4 py-1 md:py-2 rounded-md" onclick="editBlog(${blog.id})">Edit</button>
                                <button class="bg-red-600 text-white px-3 md:px-4 py-1 md:py-2 rounded-md ml-2" onclick="confirmDelete(${blog.id})">Delete</button>
                            </td>
                        </tr>`;
                });
                if (rows === '') {
                    rows = `<tr><td colspan="3" class="text-center text-gray-500 p-4">No blogs found</td></tr>`;
                }
                $("#blogTableBody").html(rows);
            })
            .catch(error => {
                console.error("Error fetching blogs:", error);
                $("#blogTableBody").html(`<tr><td colspan="3" class="text-center text-red-500 p-4">Error loading blogs. Please try again.</td></tr>`);
            });
    }
</script>

</body>
</html>
