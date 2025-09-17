# walinker_config_api
এপিআই প্রজেক্ট 
# N = যেকোনো সংখ্যা 
# Base URL = https://your_domain.com/walinker_config/api/index.php/

# Group data get page 1 (n numbder page)
End Point = group?page=1 

# Count group post all 
End Point = group?count_*

# Count group post 7 days ( n number days )
End Point = group?count_7

# Get Group of number (n number group )
https://walinker.buildbdapp.shop/walinker_config/api/index.php/group/45

# Get country filter group 
https://walinker.buildbdapp.shop/walinker_config/api/index.php/group?page=1&country=Bangladesh

# Get category filter group 
https://walinker.buildbdapp.shop/walinker_config/api/index.php/group?page=1&categories=Pakistan 

# Get category + country filter group 
https://walinker.buildbdapp.shop/walinker_config/api/index.php/group?page=1&country=Bangladesh&categories=Pakistan 

# Get filter of group name 
https://walinker.buildbdapp.shop/walinker_config/api/index.php//group?page=1&group_name=test group 

# Get report filter group (N report)
https://walinker.buildbdapp.shop/walinker_config/api/index.php/group?min_reports=3

# Get user of group ( এই ইউজারের কয়টা গুরুপ আছে )
https://walinker.buildbdapp.shop/walinker_config/api/index.php/group/by_user/24

# Delete group 
Way 1/use app id /শুধু এই এপ আইডি দিয়ে পোস্ট করা গুরুপ গুলো ডিলেট করা যাবে 
{
    "app_id": "123e4567-e89b-12d3-a456-426614174002"
}
Way 2/use bearer token/সব গুরুপ ডিলেট করা যাবে এডমিন এক্সেস
Headers: Key = Authorization / Value = Bearer 1 
Bearer 1(N তম টোকেন) DB  েত থাকা ডামি ডাটা টেবিল এ যে টোকেন সেই টোকেন দিয়ে এখানে কাজ করা যাবে 
https://walinker.buildbdapp.shop/walinker_config/api/index.php/group/80/delete






