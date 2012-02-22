# Generate .mo file
#ENG
if [ -f en_GB.UTF-8/LC_MESSAGES/teke.mo ]
then
    rm en_GB.UTF-8/LC_MESSAGES/teke.mo
fi
msgfmt -o en_GB.UTF-8/LC_MESSAGES/teke.mo en_GB.UTF-8/LC_MESSAGES/teke.po

echo "TeKe ENG Done"
#EST
if [ -f et_EE.UTF-8/LC_MESSAGES/teke.mo ]
then
    rm et_EE.UTF-8/LC_MESSAGES/teke.mo
fi
msgfmt -o et_EE.UTF-8/LC_MESSAGES/teke.mo et_EE.UTF-8/LC_MESSAGES/teke.po

echo "TeKe EST Done"

#RUS
if [ -f ru_RU.UTF-8/LC_MESSAGES/teke.mo ]
then
    rm ru_RU.UTF-8/LC_MESSAGES/teke.mo
fi
msgfmt -o ru_RU.UTF-8/LC_MESSAGES/teke.mo ru_RU.UTF-8/LC_MESSAGES/teke.po

echo "TeKe RUS Done"

