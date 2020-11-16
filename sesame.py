import requests
import json

def sesame_control(cmd):
    device_id = "ca0001ce-62d6-abc1-287c-f98bcf8b26b1"

    url = "https://api.candyhouse.co/public/sesame/" + device_id
    headers = {"Authorization": "LqdiHHnoSRW6KfWpZYZ4g75HiF8AlK9StzyfVpTpNcDsQsLUehogfqI-dYjpzKfvFyzJFG0PIYSt"}


    if cmd:
        payload_control = {"command":"lock"}
    else:
        payload_control = {"command":"unlock"}

    requests.post(url, headers=headers, data=json.dumps(payload_control))

    #json_data = r.json()
    #print(json_data)

#sesami_control(True)
#sesami_control(False)
