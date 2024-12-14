<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Tailwind DataTable</title>

    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css" rel="stylesheet">

    <!-- Responsive DataTables CSS -->
    <link href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4 text-gray-800">Responsive Tailwind DataTable</h1>

        <!-- DataTable -->
        <div class="overflow-x-auto">
            <table id="exampleTable" class="min-w-full divide-y divide-gray-200 text-sm text-gray-600">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Office</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Age</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap">Developer</td>
                        <td class="px-6 py-4 whitespace-nowrap">New York</td>
                        <td class="px-6 py-4 whitespace-nowrap">30</td>
                        <td class="px-6 py-4 whitespace-nowrap">2022-01-01</td>
                        <td class="px-6 py-4 whitespace-nowrap">$120,000</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">Jane Smith</td>
                        <td class="px-6 py-4 whitespace-nowrap">Designer</td>
                        <td class="px-6 py-4 whitespace-nowrap">London</td>
                        <td class="px-6 py-4 whitespace-nowrap">27</td>
                        <td class="px-6 py-4 whitespace-nowrap">2021-06-15</td>
                        <td class="px-6 py-4 whitespace-nowrap">$100,000</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">Mark Johnson</td>
                        <td class="px-6 py-4 whitespace-nowrap">Manager</td>
                        <td class="px-6 py-4 whitespace-nowrap">Tokyo</td>
                        <td class="px-6 py-4 whitespace-nowrap">40</td>
                        <td class="px-6 py-4 whitespace-nowrap">2018-09-23</td>
                        <td class="px-6 py-4 whitespace-nowrap">$150,000</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

    <!-- Responsive DataTables JS -->
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>

    <!-- DataTable Initialization -->
    <script>
        $(document).ready(function () {
            $('#exampleTable').DataTable({
                responsive: true, // Enable responsive behavior
                paging: true,     // Enable pagination
                searching: true,  // Enable search functionality
                info: true,       // Display table info
                order: [[0, 'asc']] // Default ordering by the first column
            });
        });
    </script>

</body>

</html>
