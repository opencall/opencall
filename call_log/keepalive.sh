res=`ps aux | grep server.php | grep -v grep | wc -l`
script_dir=$(dirname $0)
call_log_script="php "$script_dir"/server.php"
if [ $res -le 0 ]
then
    eval $call_log_script
fi
