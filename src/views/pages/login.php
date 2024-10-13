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
                    ease-linear duration-200 text-white hover:text-[#c8d8fd] mb-6">Sign In</button>
                </form>

                <div class="line__separator flex justify-between items-center"> 
                    <div class="line"></div> 
                    <div id="or__separator">OR</div> 
                    <div class="line"></div> 
                </div>

                <div class="oauth__container flex justify-between mt-5">
                    <div class="oauth__login flex">
                        <div class="oauth__icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" xmlns:xlink="http://www.w3.org/1999/xlink" style="display: block;">
                                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"></path>
                                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"></path>
                                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"></path>
                                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"></path>
                                <path fill="none" d="M0 0h48v48H0z"></path>
                            </svg>
                        </div>
                        <span class="oauth__button__contents font-semibold">Sign in with Google</span>
                    </div>

                    <div class="oauth__login flex">
                        <div class="oauth__icon">
                            <img class="object-cover" src="../../../public/assets/images/Pfizer__logo_oauth.png">
                        </div>
                        <span class="oauth__button__contents font-semibold">Sign in with Pfizer</span>
                    </div>
                </div>
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