import html
import json
import re
import urllib.request

BASE = "http://127.0.0.1:8000"

cookies = {}

def set_cookies(resp):
    for h, v in resp.headers.items():
        if h.lower() == "set-cookie":
            part = v.split(";")[0]
            k, _, val = part.partition("=")
            cookies[k] = val

def cookie_header():
    return "; ".join(f"{k}={v}" for k, v in cookies.items())

def get(path):
    req = urllib.request.Request(BASE + path, headers={"Cookie": cookie_header()})
    resp = urllib.request.urlopen(req)
    body = resp.read().decode()
    set_cookies(resp)
    return body

# 1. GET /login
page = get("/login")

snapshot_match = re.search(r'wire:snapshot="([^"]+)"', page)
snapshot = html.unescape(snapshot_match.group(1))
csrf = re.search(r'data-csrf="([^"]+)"', page).group(1)
update_uri = re.search(r'data-update-uri="([^"]+)"', page)
update_uri = update_uri.group(1) if update_uri else "/livewire/update"
print("update_uri:", update_uri)

# 2. POST update: set wrong credentials and call login
payload = {
    "_token": csrf,
    "components": [
        {
            "snapshot": snapshot,
            "updates": {"username": "salahuser", "password": "salahpass"},
            "calls": [{"path": "", "method": "login", "params": []}],
        }
    ],
}
if not update_uri.startswith("http"):
    update_uri = BASE + update_uri
req = urllib.request.Request(
    update_uri,
    data=json.dumps(payload).encode(),
    headers={
        "Content-Type": "application/json",
        "X-Livewire": "1",
        "Cookie": cookie_header(),
    },
    method="POST",
)
resp = urllib.request.urlopen(req)
body = resp.read().decode()
set_cookies(resp)
data = json.loads(body)
comp = data["components"][0]
print("EFFECTS:", json.dumps({k: v for k, v in comp.get("effects", {}).items() if k != "html"}, indent=2))
print("TOP-LEVEL KEYS:", list(data.keys()))
if "redirect" in json.dumps(data):
    print("redirect found in response")

# 3. GET /login again (simulating the redirect) and check for toast script
page2 = get("/login")
has_error_script = "KTToast" in page2 and "danger" in page2
print("toast script in redirected page:", has_error_script)
m = re.search(r"KTToast[\s\S]{0,200}", page2)
if m:
    print(m.group(0))
