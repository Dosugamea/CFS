import requests
import time
import os
import json

body = '{"dummy_token":"vhdZcU/ecad8SsyGkHgFpoUWjXgklydbWlhkWmazEpO8bqxv/27H3Vb/RO3/UsiMr5+gWoti2okZW/bsBIi7o94ju1kOkh8KcVoei1aDP/LgVvoEXsmqdwQsp73Z62czwrkJLYAkUthE+ayA2J8fYJ1l5fqPyMc8Zf0PS5pADkc=","auth_data":"ZONwqEmsNb+u2p8hA+3idpFEwPAX0YXjgbr3lV8vJShdNG+WUqBG5NSIdGtOPYrV+VZX152za2W+XKIaYCgQ9CqeWqpZppMbqAx8i81H642plOCU37oGmkwrtCTw6iFzGiUdKwj4RPWpxXFX7hUlARovCCB/d09fexNJnzVA3fMlMbA5VOgi++7dCyA45RHIsbn2nnb6VZAUWjjRAs3CxR6Yp0LiQFxnTbfOQqd+JbgdhX9/MjMnfxy19tKoogXtMYrtuJzmjCRH6XtYhQk6loftd4OeqrrvBEs0+gG40TqX1flkJAhO93wkNjShzZOdiNh+enDUuriFydgxmPvcUi4+zTQN1V/JOvCl82g/YlI="}'
headers = dict()
headers['Accept'] = '*/*'
headers['Accept-Encoding'] = 'gzip,deflate'
headers['API-Model'] = 'straightforward'
headers['Debug'] = '1'
headers['Bundle-Version'] = '5.0.2'
headers['Client-Version'] = '24.1'
headers['OS-Version'] = 'Nexus 5X google bullhead 7.1.2'
headers['OS'] = 'Android'
headers['Platform-Type'] = '2'
headers['Application-ID'] = '626776655'
headers['Time-Zone'] = 'JST'
headers['Region'] = '392'
headers['Authorize'] = 'consumerKey=lovelive_test&timeStamp=%s&version=1.1&nonce=1'%(str(int(time.time())))
headers['X-Message-Code'] ='c42fe1b00c696a9ce83c96698e0850a27c374325'
headers['User-Agent'] = None

body = {"request_data":(None,body)}

print "Getting decrypt keys..."
r = requests.post("http://prod-jp.lovelive.ge.klabgames.net/main.php/login/authkey", files=body, headers=headers)
release_info = json.loads(r.text)['release_info']
file = open("conf.json","w")
file.write(json.dumps(release_info))
file.close()
print "Got release_info"
print release_info