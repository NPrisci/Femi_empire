<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>
    $(document).ready(function() {
        $(".service-list-1.owl-carousel").owlCarousel({
            loop: true,
            margin: 10,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 3800,
            autoplayHoverPause: true,
            navText: ["&#8592;", "&#8594;"],
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        });
    });
</script>
<!-- page de connexion  -->
<script>
    function togglePwd(id, btn) {
        const inp = document.getElementById(id);
        if (inp.type === 'password') {
            inp.type = 'text';
            btn.textContent = '🙈';
        } else {
            inp.type = 'password';
            btn.textContent = '👁';
        }
    }
</script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // ========== FILTRE PORTFOLIO ==========
    const filterItems = document.querySelectorAll('.portfolio_menu ul li');
    const gridItems = document.querySelectorAll('.image_load .grid-item');
    
    if (filterItems.length > 0 && gridItems.length > 0) {
        filterItems.forEach(item => {
            item.addEventListener('click', function() {
                // Retirer la classe active de tous les filtres
                filterItems.forEach(el => el.classList.remove('active'));
                // Ajouter la classe active sur le filtre cliqué
                this.classList.add('active');
                
                const filterValue = this.getAttribute('data-filter');
                
                gridItems.forEach(gridItem => {
                    if (filterValue === '*' || gridItem.classList.contains(filterValue.replace('.', ''))) {
                        gridItem.style.display = 'block';
                        gridItem.classList.remove('hide');
                    } else {
                        gridItem.style.display = 'none';
                        gridItem.classList.add('hide');
                    }
                });
            });
        });
    }
    
    // ========== ANIMATION DES COUNTERS ==========
    const counters = document.querySelectorAll('.counter');
    if (counters.length > 0) {
        const speed = 200;
        
        counters.forEach(counter => {
            const updateCount = () => {
                const target = parseInt(counter.getAttribute('data-target')) || parseInt(counter.textContent);
                const count = parseInt(counter.textContent);
                const increment = target / speed;
                
                if (count < target) {
                    counter.textContent = Math.ceil(count + increment);
                    setTimeout(updateCount, 20);
                } else {
                    counter.textContent = target;
                }
            };
            
            // Démarrer l'animation quand l'élément est visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const initialValue = counter.getAttribute('data-target') || counter.textContent;
                        counter.setAttribute('data-target', initialValue);
                        counter.textContent = '0';
                        updateCount();
                        observer.unobserve(counter);
                    }
                });
            }, { threshold: 0.5 });
            
            observer.observe(counter);
        });
    }
    
    // ========== OWL CAROUSEL SERVICES ==========
    if (typeof $ !== 'undefined' && $.fn.owlCarousel) {
        $('.service-list-1').owlCarousel({
            loop: true,
            margin: 30,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 4000,
            smartSpeed: 800,
            responsive: {
                0: {
                    items: 1
                },
                576: {
                    items: 2
                },
                992: {
                    items: 3
                },
                1200: {
                    items: 4
                }
            },
            navText: [
                '<i class="bi bi-chevron-left"></i>',
                '<i class="bi bi-chevron-right"></i>'
            ]
        });
    }
});
</script>
<!-- jquery js -->
<script src="assets/js/vendor/jquery-3.6.2.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- bootstrap js -->
<script src="assets/js/bootstrap.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.min.js" integrity="sha512-nKXmKvJyiGQy343jatQlzDprflyB5c+tKCzGP3Uq67v+lmzfnZUi/ZT+fc6ITZfSC5HhaBKUIvr/nTLCV+7F+Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- carousel js -->
<script src="assets/js/owl.carousel.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js" integrity="sha512-9CWGXFSJ+/X0LWzSRCZFsOPhSfm6jbnL+Mpqo0o8Ke2SYr8rCTqb4/wGm+9n13HtDE1NQpAEOrMecDZw4FXQGg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- counterup js -->
<script src="assets/js/wow.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Counter-Up/1.0.0/jquery.counterup.min.js" integrity="sha512-d8F1J2kyiRowBB/8/pAWsqUl0wSEOkG5KATkVV4slfblq9VRQ6MyDZVxWl2tWd+mPhuCbpTB4M7uU/x9FlgQ9Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- imagesloaded js -->
<script src="assets/js/imagesloaded.pkgd.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.imagesloaded/5.0.0/imagesloaded.pkgd.min.js" integrity="sha512-kfs3Dt9u9YcOiIt4rNcPUzdyNNO9sVGQPiZsub7ywg6lRW5KuK1m145ImrFHe3LMWXHndoKo2YRXWy8rnOcSKg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- venobox js -->
<script src="venobox/venobox.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/venobox/2.1.8/venobox.min.js" integrity="sha512-LvcjoBF1sjBfiAJpi1Vt5pJXcT7A+0BK6nvwYkp0PwL3zNswVsRi3GURZXlRN8o6E9p0pJUJi5vsp6LSqVBzhw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!--  animated-text js -->
<script src="assets/js/animated-text.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Morphext/2.4.4/morphext.min.js" integrity="sha512-WPYLBBtLFRUKCbj+PI7iHTL9ORQvxc7uhsb7bIQMKoRPYbiybfoIKIxJ7ynVwrGpspeR2K7WFODUST/xDLg1wA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- venobox min js -->
<script src="venobox/venobox.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/venobox/2.1.8/venobox.js" integrity="sha512-UQu+ReaHRRCFphBEPzVBfUrhSMgurKVnywb203FkiJ2RsibTmCiwxJ4YJ1rKimPJE61+iDVVjAIGRIwREiOLJw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- isotope js -->
<script src="assets/js/isotope.pkgd.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js" integrity="sha512-Zq2BOxyhvnRFXu0+WE6ojpZLOU2jdnqbrM1hmVdGzyeCa1DgM3X5Q4A/Is9xA1IkbUeDd7755dNNI/PzSf2Pew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- jquery meanmenu js -->
<script src="assets/js/jquery.meanmenu.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.mmenu/4.7.0/js/jquery.mmenu.min.all.min.js" integrity="sha512-1+zap5AVkJjkuxft8rgAWLYfDdTiKK00foOwcqlWyhkbDgeSQCurMHWGI+bN3PN+udXmuPLlJCZpXNYQI7sUDg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- theme js -->
<script src="
https://cdn.jsdelivr.net/npm/theme@0.1.0/dist/theme.min.js
"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="assets/js/theme.js"></script>
<script src="assets/js/coustom-carousel.js"></script>
<!-- coustom js -->
<script src="assets/js/gsap.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.13.0/gsap.min.js" integrity="sha512-NcZdtrT77bJr4STcmsGAESr06BYGE8woZdSdEgqnpyqac7sugNO+Tr4bGwGF3MsnEkGKhU2KL2xh6Ec+BqsaHA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<!-- <script src="assets/js/ScrollTrigger.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/ScrollTrigger/1.0.6/ScrollTrigger.min.js" integrity="sha512-+LXqbM6YLduaaxq6kNcjMsQgZQUTdZp7FTaArWYFt1nxyFKlQSMdIF/WQ/VgsReERwRD8w/9H9cahFx25UDd+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="assets/js/SplitText.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/split.js/1.6.2/split.min.js" integrity="sha512-to2k78YjoNUq8+hnJS8AwFg/nrLRFLdYYalb18SlcsFRXavCOTfBF3lNyplKkLJeB8YjKVTb1FPHGSy9sXfSdg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="assets/js/text-animation.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/textify.js/3.0.0/Textify.min.js" integrity="sha512-8Iqxmjkiw3lwDf6cTHKJfoeg7fZdcLiYq15AkJfqYXiL43CEtUsX9T6lJufT17St6myVDtaaZ8MRD53SEAPt7Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
<script src="assets/js/user-icon.js"></script>
<script src="assets/js/forms.js"></script>