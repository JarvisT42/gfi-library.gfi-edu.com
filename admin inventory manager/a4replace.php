<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Replacement Slip</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@latest/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="a4.css"></head>
<body>
<button class="print-button" onclick="window.print()">Print Document</button>

    <page size="A4">
        <div class="wrapper">
            <div id="one" class="mydiv">
                <div id="printableArea" class="border-4 border-purple-700">
                    <div class="text-center p-2">
                        <h2 class="text-xl font-semibold">Gensantos Foundation College Inc.</h2>
                        <p class="text-sm text-muted-foreground">Bulaong Extension Brgy. Dadiangas West General Santos City</p>
                        <p class="text-lg font-semibold">Replacement Slip</p>
                    </div>
                    <div class="p-2">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2 border border-gray-300 rounded">
                                <label for="title">Book Title:</label>
                                <p id="title" class="w-full"><?php echo htmlspecialchars($_GET['title']); ?></p>
                            </div>
                            <div class="space-y-2 border border-gray-300 rounded">
                                <label for="category">Category:</label>
                                <p id="category" class="w-full"><?php echo htmlspecialchars($_GET['category']); ?></p>
                            </div>
                        </div>
                        <div class="space-y-2 border border-gray-300 rounded">
                            <label for="overdueFines">Overdue Fines:</label>
                            <p id="overdueFines" class="w-full"><?php echo htmlspecialchars($_GET['overdueFines']); ?></p>
                        </div>
                        <div class="space-y-2 border border-gray-300 rounded">
                            <label for="replacementAmount">Replacement Fee:</label>
                            <p id="replacementAmount" class="w-full">₱<?php echo htmlspecialchars($_GET['replacementAmount']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </page>
</body>
</html>
