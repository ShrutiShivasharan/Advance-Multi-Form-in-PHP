<?php
include('./connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi Form - advance</title>
</head>
<style>
    .step { display : none;}
    .active { display : block;}
</style>
<body>
    <div class="container">
        <form id="multiStepForm">
            <input type="hidden" name="step" id="step" value="1"> 
            <!-- step 1 -->
            <div class="step active" id="step1">
                <h2>Step : 1 - Login Details</h2>
                <label>Email</label>
                <input type="text" id='email' name='email' required>
                <label>Password</label>
                <input type="text" id='password' name='password' required>
                <button type="button" onclick="submitForm(1)">Next</button>
            </div>
            <!-- step 2 -->
            <div class="step" id="step2">
                <h2>Step : 2 - Personal Details</h2>
                <label>Full Name</label>
                <input type="text" id='name' name='name' required>
                <label>Phone Number</label>
                <input type="text" id='phone' name='phone' required>
                <label>Full Address</label>
                <input type="text" id='address' name='address' required>
                <!-- <button type="button">Previous</button> -->
                <button type="button" onclick="submitForm(2)">Next</button>
            </div>
            <!-- step 3 -->
            <div class="step" id="step3">
                <h2>Step : 3 - Confirmation</h2>
                <p>Review your details...</p>
                <p><strong>Full Name: <span id="reviewName"></span> </strong></p>
                <p><strong>Email: <span id="reviewEmail"></span> </strong></p>
                <p><strong>Phone: <span id="reviewPhone"></span> </strong></p>
                <p><strong>Full Address: <span id="reviewAddress"></span> </strong></p>
            </div>
        </form>
    </div>

<script>
    function submitForm(step){
        const formData = new FormData();
        formData.append("step", step);

        if(step === 1){
            formData.append("email", document.getElementById("email").value);
            formData.append("password", document.getElementById("password").value);
        }else if(step === 2){
            formData.append("name", document.getElementById("name").value);
            formData.append("phone", document.getElementById("phone").value);
            formData.append("address", document.getElementById("address").value);
        }

        fetch("submitForm.php",{
            method : "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success || data === "success"){
                document.getElementById("step" + step).classList.remove("active");
                document.getElementById("step" + (step + 1)).classList.add("active");

                if(step === 2 ){
                    document.getElementById("reviewName").innerText = document.getElementById("name").value;
                    document.getElementById("reviewEmail").innerText = document.getElementById("eamil").value;
                    document.getElementById("reviewPhone").innerText = document.getElementById("phone").value;
                    document.getElementById("reviewAddress").innerText = document.getElementById("address").value;
                }
            }else if(data.name){
                document.getElementById("step1").classList.remove("active");
                document.getElementById("step2").classList.remove("active");
                document.getElementById("step3").classList.add("active");

                //if user data alredy exist then direct display step 3
                document.getElementById("reviewName").innerText = data.name;
                document.getElementById("reviewEmail").innerText = data.email;
                document.getElementById("reviewPhone").innerText = data.phone;
                document.getElementById("reviewAddress").innerText = data.address;
            }else if(data === "Incorrect_Password"){
                alert("User Email exist, but password not matched!!");
            }else{
                alert("Error", data);
            }
        })
        .catch(error => console.log("Error", error));
    }
</script>

</body>
</html>