<script type="text/javascript">
	window.onload = function() {
		var bindEvent = {
	        'add':function(name,type,fn,isBubble){
	            var dom = document.getElementsByName(name)[0];
	            if(!isBubble) isBubble=false;
	            if(dom.addEventListenner){
	                dom.addEventListenner(type,fn,isBubble);
	            }else if(dom.attachEvent){
	                Transit = function(){
	                    fn.call(dom);
	                }
	                dom.attachEvent('on'+type,Transit);
	            }else{
	                dom['on'+type] = fn;
	            }
	        },
	        'remove':function(name,type,fn,isBubble){
	            var dom = document.getElementsByName(name)[0];
	            if(!isBubble) isBubble=false;
	            if(dom.removeEventListenner){
	                dom.removeEventListenner(type,fn,isBubble)
	            }else if(dom.detachEvent){
	                dom.detachEvent('on'+type,Transit)
	            }else{
	                dom['on'+type]=null;
	            }
	        }
	    }

	    var name1 = 'aaa';
	    var name2 = 'bbb';
		var aaa = document.getElementsByName(name1)[0];
		var bbb = document.getElementsByName(name2)[0];

		function cb() {
			if (aaa.value == 'off') {
				bbb.readOnly = true;
				bbb.value = '';
			} else {
				bbb.readOnly = false;
			}
		}

		var str = window.navigator.userAgent;
	    if(str.indexOf("MSIE") != -1){
	        bindEvent.add(name1, 'change', cb, true); // IE
	    }else{
	        bindEvent.add(name1, 'change', cb, false); // 非IE
	    }
	}
</script>