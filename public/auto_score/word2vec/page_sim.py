#!/usr/bin/env python2.7
# -*- coding: utf-8 -*-

from __future__ import print_function

# 防止中文编码报错
import sys

reload(sys)
sys.setdefaultencoding('utf-8')
import jieba.posseg as pseg
from jieba import analyse
import codecs
import numpy
import gensim
import numpy as np
import get_keyword as gKey
import json
import os
import time
import datetime

# 提取关键词
def keyword_extract(doc_list, data, file_name):
    seg_list = gKey.seg_to_list(data, False)
    filter_list = gKey.word_filter(seg_list, False)
    keywords = gKey.tfidf_extract_str(doc_list, filter_list)

    # tfidf = analyse.extract_tags
    # keywords = tfidf(data)
    return keywords

# 提取关键词
def keyword_extract1(data, file_name):
    tfidf = analyse.extract_tags
    keywords = tfidf(data)
    return keywords

# 对文档每句话提取关键词
def get_keywords(doc_list, docpath, savepath):
    with open(docpath, 'r') as docf, open(savepath, 'w') as outf:
        for data in docf:
            data = data.strip()
            # keywords = keyword_extract(doc_list, data, savepath)
            # outf.write(keywords)
            keywords = keyword_extract1(data, savepath)
            for k in keywords:
                outf.write(k + " ")


def get_char_pos(string,char):
    chPos=[]
    try:
        chPos=list(((pos) for pos,val in enumerate(string) if(val == char)))
    except:
        pass
    return chPos

# 从txt文档中读取关键词,获取词向量
def word2vec(file_name, model):
    wordvec_size=200
    with codecs.open(file_name, 'r') as f:
        word_vec_all = numpy.zeros(wordvec_size)
        for data in f:
            words = data[:-1].split(' ')
            for word in words:
                if word in model.wv.index2word:
                    word_vec_all = word_vec_all + model[unicode(word, 'utf-8')]
        return word_vec_all

# 计算余弦相似度
def simlarityCalu(vector1,vector2):
    vector1Mod=np.sqrt(vector1.dot(vector1))
    vector2Mod=np.sqrt(vector2.dot(vector2))
    if vector2Mod!=0 and vector1Mod!=0:
        simlarity=(vector1.dot(vector2))/(vector1Mod*vector2Mod)
    else:
        simlarity=0
    return simlarity

if __name__ == '__main__':
    argv = sys.argv
    startTime = datetime.datetime.now()
    model = gensim.models.Word2Vec.load('./auto_score/word2vec/dataset.word2vec')
    p1 = './auto_score/answer_log/'+argv[1]
    p2 = './auto_score/answer_log/'+argv[2]
    with open('./auto_score/word2vec/dataset_all_list.txt', 'r') as f:
        content = f.read()
        doc_list = json.loads(content, encoding='utf-8')
    p1_keywords = './auto_score/answer_log/'+argv[1][:-4]+'_keywords.txt'
    p2_keywords = './auto_score/answer_log/'+argv[2][:-4]+'_keywords.txt'
    if os.path.isfile(p1_keywords) == False:
        get_keywords(doc_list, p1, p1_keywords)
    if os.path.isfile(p2_keywords) == False:
        get_keywords(doc_list, p2, p2_keywords)
    p1_vec=word2vec(p1_keywords,model)
    p2_vec=word2vec(p2_keywords,model)
    sim = simlarityCalu(p1_vec,p2_vec)

    endTime = datetime.datetime.now()
    seconds = (endTime-startTime).seconds

    # log
    month = time.strftime("%Y%m", time.localtime())
    with open('./auto_score/answer_log/'+month+'.txt', 'a') as l:
        l.write('(word2vec) Similarity between '+p1+' and '+p2+' : '+str(sim)+';    cost: '+str(seconds)+' seconds')
        l.write('\n')

    print(sim)