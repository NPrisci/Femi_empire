<!-- =========================================
     WHATSAPP FLOAT BUTTON
========================================= -->

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>

.whatsapp-float{
    position: fixed;
    width: 65px;
    height: 65px;
    bottom: 95px;
    right: 25px;

    background: #25D366;
    color: #fff;

    border-radius: 50%;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 34px;

    text-decoration: none;

    z-index: 99999;

    box-shadow: 0 6px 18px rgba(0,0,0,0.25);

    transition: all 0.3s ease;

    animation: whatsappPulse 2s infinite;
}

.whatsapp-float:hover{
    transform: scale(1.1);
    background: #1ebe5d;
}

.whatsapp-label{
    position: fixed;

    bottom: 95px;
    right: 100px;

    background: #e2836a;

    color: #fdfcfc;

    padding: 10px 14px;

    border-radius: 12px;

    font-size: 14px;
    font-weight: 500;

    box-shadow: 0 4px 12px rgba(0,0,0,0.12);

    z-index: 99998;

    animation: fadeInUp 0.5s ease;
}

@keyframes whatsappPulse{

    0%{
        box-shadow: 0 0 0 0 rgba(37,211,102,0.6);
    }

    70%{
        box-shadow: 0 0 0 18px rgba(37,211,102,0);
    }

    100%{
        box-shadow: 0 0 0 0 rgba(37,211,102,0);
    }
}

@keyframes fadeInUp{

    from{
        opacity: 0;
        transform: translateY(10px);
    }

    to{
        opacity: 1;
        transform: translateY(0);
    }
}

@media(max-width:768px){

    .whatsapp-float{
        width: 58px;
        height: 58px;
        font-size: 30px;
        bottom: 20px;
        right: 20px;
    }

    .whatsapp-label{
        display: none;
    }
}

</style>

<!-- Label -->
<div class="whatsapp-label">
    Besoin d'aide ?
</div>

<!-- Bouton -->
<a href="https://wa.me/2290100000000?text=Bonjour%20FEMI%20Fairy%20Finger%20👋"
   class="whatsapp-float"
   target="_blank">

    <i class="fab fa-whatsapp"></i>

</a>