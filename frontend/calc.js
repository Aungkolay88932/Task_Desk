let expenses = JSON.parse(localStorage.getItem('myExpenses')) || [];

function addExpense() {
    const item = document.getElementById('item-name').value;
    const amount = parseFloat(document.getElementById('amount').value);
    const category = document.getElementById('category').value;
    const date = document.getElementById('date-input').value;

    if (item && amount && date) {
        const entry = { item, amount, category, date: new Date(date) };
        expenses.push(entry);
        localStorage.setItem('myExpenses', JSON.stringify(expenses));
        alert("Expense Added!");
        clearInputs();
    } else {
        alert("Please fill in all fields");
    }
}

function clearInputs() {
    document.getElementById('item-name').value = "";
    document.getElementById('amount').value = "";
    document.getElementById('category').value = "";
    document.getElementById('date-input').value = "";
}

function displayAll() {
    const screen = document.getElementById('display-screen');
    screen.innerHTML = '<h3 style="background-color: white; ">All Records:</h3><br>';
    expenses.forEach(ex => {
        screen.innerHTML += `<p  style="background-color: white; ">${new Date(ex.date).toLocaleDateString()} - ${ex.item}: $${ex.amount} (${ex.category})</p>`;
    });
}

function calculateTotal(period) {
    const screen = document.getElementById('display-screen');
    const today = new Date();
    let total = 0;

    expenses.forEach(ex => {
        const exDate = new Date(ex.date);
        
        if (period === 'day' && exDate.toDateString() === today.toDateString()) {
            total += ex.amount;
        } else if (period === 'month' && exDate.getMonth() === today.getMonth()) {
            total += ex.amount;
        }
        // Simplified Week logic: within last 7 days
        else if (period === 'week' && (today - exDate) / (1000 * 60 * 60 * 24) <= 7) {
            total += ex.amount;
        }
    });

    screen.innerHTML = `<h2 style="background-color: white; ">${period.toUpperCase()} TOTAL</h2><hr><br><h1 style="font-size:4rem; background-color: white; ">$${total.toFixed(2)}</h1>`;
}

function cleardata() {
    // 1. Clear the main display screen (the large area)
    const screen = document.getElementById('display-screen');
    if (screen) {
        screen.innerHTML = ""; // This makes the large display area blank
    }

    // 2. Clear all top input fields
    // Note: Ensure these IDs match what you have in your HTML
    if(document.getElementById('item-name')) document.getElementById('item-name').value = "";
    if(document.getElementById('amount')) document.getElementById('amount').value = "";
    if(document.getElementById('category')) document.getElementById('category').value = "";
    if(document.getElementById('date')) document.getElementById('date').value = "";

    // 3. Clear the footer date input (from your HTML screenshot)
    const footerDate = document.getElementById('footer-date');
    if (footerDate) {
        footerDate.value = "";
    }

    console.log("All data cleared and screen is now empty.");
}

// Logout confirmation function
function confirmLogout() {
    // Show confirmation dialog
    const result = confirm("Are you sure do you want to log out?");
    
    // If user clicks OK (true), redirect to login page
    if (result) {
        window.location.href = "login.html";
    }
    // If user clicks Cancel (false), nothing happens - alert box closes automatically
}
