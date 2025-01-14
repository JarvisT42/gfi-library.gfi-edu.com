<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="a4.css">
</head>
<body>
    <!-- Print button -->
    <button class="print-button" onclick="window.print()">Print Document</button>

    <!-- Page layout (single page) -->
    <page size="A4">
        <div class="wrapper">
            <div id="one" class="mydiv">
                <div id="printableArea" class="border-4 border-purple-700">
                    <div class="text-center p-2">
                        <h2 class="text-xl font-semibold">Gensantos Foundation College Inc.</h2>
                        <p class="text-sm text-muted-foreground">Bulaong Extension Brgy. Dadiangas West General Santos City</p>
                        <p class="text-lg font-semibold">LIBRARY OVERDUE SLIP</p>
                    </div>
                    <div class="p-2">
                        <div class="grid grid-cols-2">
                            <div class="space-y-2 border border-gray-300 rounded">
                                <label for="name">NAME:</label>
                                <p id="name" class="w-full"><?php echo htmlspecialchars($_GET['name']); ?></p>
                            </div>
                            <div class="space-y-2 border border-gray-300 rounded">
                                <label for="date">DATE:</label>
                                <p id="date" class="w-full"><?php echo htmlspecialchars($_GET['date']); ?></p>
                            </div>
                        </div>
                        <div class="">
                            <label for="books">NO. OF BOOK/S BORROWED:</label>
                            <p id="books" class="w-full"><?php echo htmlspecialchars($_GET['books']); ?></p>
                        </div>
                        <div class="">
                            <label for="days">DAY/S OVERDUE:</label>
                            <p id="days" class="w-full"><?php echo htmlspecialchars($_GET['days']); ?></p>
                        </div>
                        <div class="">
                            <label for="amount">TOTAL AMOUNT TO BE PAID:</label>
                            <p id="amount" class="w-full"><?php echo htmlspecialchars($_GET['amount']); ?></p>
                        </div>
                         <div class="mt-6">
                            <label for="approved-by" class="block text-lg font-medium">APPROVED BY:</label>
                            <div class="border-b border-gray-500 w-1/2 mt-2"></div>
                            <p class="text-sm text-gray-500 mt-1">(Signature over printed name)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </page>
</body>
</html>
