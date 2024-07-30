let menu =document.querySelector('#menu-btn');
let navbar =document.querySelector('.navbar');

menu.onclick = () =>{
    menu.classList.toggle('fa-times');
    navbar.classList.toggle('active');
}

window.onscroll = () =>{
    menu.classList.remove('fa-times');
    navbar.classList.remove('active');
    }

function test(){
    //retrieving data
    var uid = document.getElementById("your name").value;
    var num = document.getElementById("your number").value;
    var email = document.getElementById("your email").value;
   
    //storing data
    var name = localStorage.setItem("uid",uid);
    var number =localStorage.setItem("num",num);
    var email = localStorage.setItem("email",email);
}

document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorMessage = document.getElementById('errorMessage');

    // Simple front-end validation
    if (username === '' || password === '') {
        errorMessage.textContent = 'Please fill in all fields.';
        errorMessage.style.display = 'block';
        return;
    }

    // Example credentials for demonstration purposes
    const correctUsername = 'admin';
    const correctPassword = 'password123';

    if (username === correctUsername && password === correctPassword) {
        // Successful login logic here (e.g., redirect to dashboard.html)
        window.location.href = 'index.html'; // Replace with the actual filename
    } else {
        errorMessage.textContent = 'Invalid username or password.';
        errorMessage.style.display = 'block';
    }
});
document.addEventListener('DOMContentLoaded', function() {
    // Handle cancel booking button click
    document.getElementById('cancelBookingBtn').addEventListener('click', function() {
        // Update form action to cancel_booking.php
        document.getElementById('bookingForm').setAttribute('action', 'cancel_booking.php');
        
        // Submit the form
        document.getElementById('bookingForm').submit();
    });
});

