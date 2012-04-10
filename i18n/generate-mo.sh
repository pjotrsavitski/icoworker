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

#FIN
if [ -f fi_FI.UTF-8/LC_MESSAGES/teke.mo ]
then
	rm fi_FI.UTF-8/LC_MESSAGES/teke.mo
fi
msgfmt -o fi_FI.UTF-8/LC_MESSAGES/teke.mo fi_FI.UTF-8/LC_MESSAGES/teke.po

echo "TeKe FIN Done"

#SWE
if [ -f sv_SE.UTF-8/LC_MESSAGES/teke.mo ]
then
    rm sv_SE.UTF-8/LC_MESSAGES/teke.mo
fi
msgfmt -o sv_SE.UTF-8/LC_MESSAGES/teke.mo sv_SE.UTF-8/LC_MESSAGES/teke.po

echo "TeKe SWE Done"
