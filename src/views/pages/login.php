<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaSync</title>

    <link rel="stylesheet" href="../../../public/assets/css/output.css">
    <link rel="stylesheet" href="../../../public/assets/css/login.css">
    <link rel="stylesheet" href="../../../public/assets/css/main.css">

    <script src="https://kit.fontawesome.com/c3d300ba56.js" crossorigin="anonymous"></script>
</head>
<body class="bg-white md:bg-[#F1F4F9]">
    <main class="grid grid-cols-1 md:grid-cols-12 h-full">
        <!-- Card Container (Form) -->
        <div class="card__container w-full md:col-span-6 order-2 md:order-1">
            <div class="card p-3 md:p-12 rounded-xl w-11/12 md:w-7/12">
                <div class="header text-center pb-12">
                    <h1 class="text-3xl font-bold">Sign In</h1>
                    <p class="text-[#8D8D8D]">Welcome! Please enter your details.</p>
                </div>

                <form action="/login" method="POST" class="flex flex-col">
                    <div class="flex flex-col mb-7">
                        <label class="input__box" for="email">Email</label>
                        <input type="email" name="email" placeholder="Email">
                    </div>

                    <div class="flex flex-col mb-2 relative">
                        <label class="input__box" for="password">Password</label>
                        <div>
                            <input type="password" name="password" id="password" placeholder="Password" class="p-2 border border-gray-300 rounded w-full">
                            <span id="togglePassword" class="eye__container absolute right-3 top-7 cursor-pointer disabled opacity-50">
                                <i class="fa-solid fa-eye-slash"></i>
                            </span>
                        </div> 
                    </div>


                    <div class="mb-10 flex justify-between">
                        <div class="flex items-center">
                            <input type="checkbox" name="remember" id="remember__box">
                            <label for="remember__box">Remember me</label>
                        </div>

                        <div class="">
                            <a href="#" class="forgot__password__link text-[#3F70ED] font-medium">Forget Password?</a>
                        </div>
                    </div>

                    <button type="submit" class="font-bold text-lg 
                    bg-[#3F70ED] hover:bg-[#5384ff] 
                    ease-linear duration-200 text-white hover:text-[#c8d8fd]">Sign In</button>
                </form>
            </div>
        </div>

        <!-- Hero Container (Image) -->
        <div class="hero__container order-1 md:order-2 hidden md:block md:col-span-6">
            <img class="w-full h-48 md:h-auto object-cover" src="https://t3.ftcdn.net/jpg/01/06/33/74/360_F_106337464_PqrDoCy2HhtDr6ezOGq13hvpVFcMGpQV.jpg">
        </div>
    </main>


    <script src="../../../public/assets/js/login.js"></script>

</body>
</html>