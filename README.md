# walinker_config_api
এপিআই প্রজেক্ট 
# N = যেকোনো সংখ্যা 
# Base URL = https://your_domain.com/walinker_config/api/index.php/

# Group data get page 1 (n numbder page)
https://your_domain.com/walinker_config/api/index.php/group?page=1 

# Count group post all 
https://your_domain.com/walinker_config/api/index.php/group?count_*

# Count group post 7 days ( n number days )
https://your_domain.com/walinker_config/api/index.php/group?count_7

# Get Group of number (n number group )
https://your_domain.com/walinker_config/api/index.php/group/45

# Get Group of User id 
https://your_domain.com/walinker_config/api/index.php/group/by_app_id/your app id 63cracketer

# Get country filter group 
https://your_domain.com/walinker_config/api/index.php/group?page=1&country=Bangladesh

# Get category filter group 
https://your_domain.com/walinker_config/api/index.php/group?page=1&categories=Pakistan 

# Get category + country filter group 
https://your_domain.com/walinker_config/api/index.php/group?page=1&country=Bangladesh&categories=Pakistan 

# Get filter of group name 
https://your_domain.com/walinker_config/api/index.php//group?page=1&group_name=test group 

# Get report filter group (N report)
https://your_domain.com/walinker_config/api/index.php/group?min_reports=3

# Get user of group ( এই ইউজারের কয়টা গুরুপ আছে )
https://your_domain.com/walinker_config/api/index.php/group/by_user/24

# Delete group 
Way 1/use app id /শুধু এই এপ আইডি দিয়ে পোস্ট করা গুরুপ গুলো ডিলেট করা যাবে 
{
    "app_id": "123e4567-e89b-12d3-a456-426614174002"
}
Way 2/use bearer token/সব গুরুপ ডিলেট করা যাবে এডমিন এক্সেস
Headers: Key = Authorization / Value = Bearer 1 
Bearer 1(N তম টোকেন) DB  েত থাকা ডামি ডাটা টেবিল এ যে টোকেন সেই টোকেন দিয়ে এখানে কাজ করা যাবে 
https://your_domain.com/walinker_config/api/index.php/group/80/delete

# Post group 
body = {
  "categories": 3,
  "group_link": "https://chat.whatsapp.com/AbCdEfGh13",
  "country": 2,
  "app_id": "123e4567-e89b-12d3-a456-426614174002"
}
https://your_domain.com/walinker_config/api/index.php/group?page=1

# Click log count 
https://your_domain.com/walinker_config/api/index.php/click_log?count

# Click log post 
body = {
  "user_id": 18,
  "group_id": 45
}

https://your_domain.com/walinker_config/api/index.php/click_log 

# Viwe log post 
body = {
  "app_id": 17,
  "group_id": 45
}

https://your_domain.com/walinker_config/api/index.php/view_log

# Report log post 
body = {
  "app_id": 24,
  "group_id": 45
}

https://your_domain.com/walinker_config/api/index.php/report_log

# Create new user 
body = {
  "app_id": "123e4567-e89b-12d3-a456-426614174008"
}
https://your_domain.com/walinker_config/api/index.php/users

# User Validate 
body = {
  "app_id": "123e4567-e89b-12d3-a456-426614174008"
}
https://your_domain.com/walinker_config/api/index.php/users/validate

# Count user 
https://your_domain.com/walinker_config/api/index.php/users

# Count User a days 
https:/your_domain.com/walinker_config/api/index.php/users?days=7

# All user delels 
https://your_domain.com/walinker_config/api/index.php/users/details

# Deasable User 
body = {
  "user_id": 25

}
https://your_domain.com/walinker_config/api/index.php/users/disable

# Group post un panding and set group+image_link+status
body = {
  "group_id": 46,
  "group_name": "Test Group",
  "image_link": "http://example.com/img.jpg",
  "status": "active"
}
https://your_domain.com/walinker_config/api/index.php/group_info

# Get group panding 
https://your_domain.com/walinker_config/api/index.php/group_info

# Admin controlar 
Headers: Key = Authorization / Value = Bearer 1 
body = {
  "admin_controlar_id": "1",
  "help": "Your data ",
  "service": "Your data ",
  "policy": "Your data ",
  "updating": "Your data ",
  "home_notification": "Your data",
  "server_activity": "0"
}
//এখানে সবগুলো একসাথে আপডেট করা যায় এবং একটি একটি করে আপডেট বা গেট করা যায় 
https://your_domain.com/walinker_config/api/index.php/admincontrolar

# Country data get 
https://your_domain.com/walinker_config/api/index.php/country

# Country data post 
Headers: Key = Authorization / Value = Bearer 1 
body = {
    "country_name": "Bangladesh"
}
https://your_domain.com/walinker_config/api/index.php/country

# Country data updte/patch 
Headers: Key = Authorization / Value = Bearer 1 
{
    "country_name": "Bangladesh Updated"
}
https://your_domain.com/walinker_config/api/index.php/country/5

# Country data delete 
Headers: Key = Authorization / Value = Bearer 1
https://your_domain.com/walinker_config/api/index.php/country/7

# Category data get 
https://your_domain.com/walinker_config/api/index.php/categories

# Category data post 
Headers: Key = Authorization / Value = Bearer 1
body = {
    "category_name": "Electronics"
}
https://your_domain.com/walinker_config/api/index.php/categories

# Category data update/patch
Headers: Key = Authorization / Value = Bearer 1
body = {
    "category_name": "Electronics & Gadgets"
}
https://your_domain.com/walinker_config/api/index.php/categories/3

# Country data delete 
Headers: Key = Authorization / Value = Bearer 1
https://your_domain.com/walinker_config/api/index.php/categories/3


# Save FMC token in database 
body = {
  "fcm_token": "abc123xyzgdgsg56545456456456545f"
}
https://your_domain.com/walinker_config/api/index.php/save_fcm_token

# Send sms get null user id 
https://your_domain.com/walinker_config/api/index.php/send_sms

# Send sms get user id + null user id data 
https://your_domain.com/walinker_config/api/index.php/send_sms?app_id=9dffda3e-153b-4b60-9799-97b6fdb726bd

# Send sms Post admin barear token 
https://your_domain.com/walinker_config/api/index.php/send_sms
body = {"user_id":5,"sms":"আমার সোনার বাংলা"}
header = Key = Authorization / Value = Bearer 123

# Send sms Delete admin derear token 
https://your_domain.com/walinker_config/api/index.php/send_sms
header = Key = Authorization / Value = Bearer 123
body= {"sms_id":5}
