<!-- Bootstrap core JavaScript -->
<script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){
        $('#categorie').on('change',function(){
            var categorieID = $(this).val();
            if(categorieID){
                $.ajax({
                type:'POST',
                url:'ajaxData.php',
                data:'categorie_id='+categorieID,
                success:function(html){
                $('#cours').html(html);
                $('#activite').html('<option value="">Sélectionnez le cours en premier</option>'); 
                }
                }); 
            }else{
                $('#cours').html('<option value="">Sélectionnez la catégorie en premier</option>');
                $('#activite').html('<option value="">Sélectionnez le cours en premier</option>'); 
            }
        });

        $('#cours').on('change',function(){
            var coursID = $(this).val();
            if(coursID){
                $.ajax({
                type:'POST',
                url:'ajaxData.php',
                data:'cours_id='+coursID,
                success:function(html){
                $('#activite').html(html);
                }
            }); 
            }else{
                 $('#activite').html('<option value="">Sélectionnez le cours en premier</option>'); 
            }
        });
    });
</script>
</body>
</html>